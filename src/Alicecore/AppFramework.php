<?php
namespace Alicecore;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppFramework extends Container implements HttpKernelInterface, TerminableInterface
{

    protected $providers = array();
    protected $booted = false; #标记系统服务是否启动

    public function __construct(array $values = array())
    {
        parent::__construct();
        $this->defaultConfig();
        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        $this->register(new Provider\HttpKernelServiceProvider());
        $this->register(new Provider\RoutingServiceProvider());
        $this->register(new Provider\ExceptionHandlerServiceProvider());
        $this->register(new Provider\EloquentServiceProvider());
        $this->register(new Provider\MemcacheServiceProvider());

        Service::start($this);
    }

    private function defaultConfig()
    {
        $this['request.http_port'] = 80;
        $this['request.https_port'] = 443;
        $this['debug'] = true;
        $this['charset'] = 'UTF-8';
        $this['logger'] = null;
        $this['static'] = __DIR__."/../../web/";
    }

    /**
     * 注册一个服务
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     */
    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        $this->providers[] = $provider;

        parent::register($provider, $values);

        return $this;
    }

    /**
     * 启动所有服务（handle方法自动调用）
     */
    public function boot()
    {
        if ($this->booted){
            return;
        }

        $this->booted = true;

        foreach ($this->providers as $provider) {
            if ($provider instanceof Provider\EventListenerProviderInterface){
                $provider->subscribe($this, $this['dispatcher']);
            }

            if ($provider instanceof Provider\BootableProviderInterface){
                $provider->boot($this);  //对未知事物的依赖，如果有就要移步至boot部分
            }
        }
    }

    /**
     * 增加一个系统事件的监听
     *
     * @param string   $eventName 监听的事件名称
     * @param callable $callback  回调处理函数
     * @param int      $priority  事件处理的优先级（越高越早）
     */
    public function on($eventName, $callback, $priority = 0)
    {
        if ($this->booted) {
            $this['dispatcher']->addListener($eventName, $this['callback_resolver']->resolveCallback($callback), $priority);
            return;
        }

        # 修改已定义的事件分发器对象（添加一个监听~）
        $this->extend('dispatcher', function (EventDispatcherInterface $dispatcher, $app) use ($callback, $priority, $eventName) {
            $dispatcher->addListener($eventName, $app['callback_resolver']->resolveCallback($callback), $priority);
            return $dispatcher;
        });
    }

    /**
     * 添加一个监听，在系统事件REQUEST执行的时候触发，注意priority优先级！
     *
     * @param mixed $callback Before filter callback
     * @param int   $priority The higher this value, the earlier an event
     *                        listener will be triggered in the chain (defaults to 0)
     */
    public function before($callback, $priority = 0)
    {
        $app = $this;

        $this->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($callback, $app) {
            if (!$event->isMasterRequest()) {
                return;
            }

            $ret = call_user_func($app['callback_resolver']->resolveCallback($callback), $event->getRequest(), $app);
            if ($ret instanceof Response) {
                $event->setResponse($ret);
            }
        }, $priority);
    }

    /**
     * 添加一个监听，在系统事件RESPONSE执行的时候触发，注意priority优先级！
     *
     * After filters are run after the controller has been executed.
     *
     * @param mixed $callback After filter callback
     * @param int   $priority The higher this value, the earlier an event
     *                        listener will be triggered in the chain (defaults to 0)
     */
    public function after($callback, $priority = 0)
    {
        $app = $this;

        $this->on(KernelEvents::RESPONSE, function (FilterResponseEvent $event) use ($callback, $app) {
            if (!$event->isMasterRequest()) {
                return;
            }

            $response = call_user_func($app['callback_resolver']->resolveCallback($callback), $event->getRequest(), $event->getResponse(), $app);
            if ($response instanceof Response) {
                $event->setResponse($response);
            } elseif (null !== $response) {
                throw new \RuntimeException('An after middleware returned an invalid response value. Must return null or an instance of Response.');
            }
        }, $priority);
    }

    /**
     * 添加一个监听，在系统事件TERMINATE执行的时候触发，注意priority优先级！
     *
     * Finish filters are run after the response has been sent.
     *
     * @param mixed $callback Finish filter callback
     * @param int   $priority The higher this value, the earlier an event
     *                        listener will be triggered in the chain (defaults to 0)
     */
    public function finish($callback, $priority = 0)
    {
        $app = $this;

        $this->on(KernelEvents::TERMINATE, function (PostResponseEvent $event) use ($callback, $app) {
            call_user_func($app['callback_resolver']->resolveCallback($callback), $event->getRequest(), $event->getResponse(), $app);
        }, $priority);
    }

     /**
     * Aborts the current request by sending a proper HTTP error.
     *
     * @param int    $statusCode The HTTP status code
     * @param string $message    The status message
     * @param array  $headers    An array of HTTP headers
     */
    public function abort($statusCode, $message = '', array $headers = array())
    {
        throw new HttpException($statusCode, $message, null, $headers);
    }

    /**
     * Registers an error handler.
     *
     * Error handlers are simple callables which take a single Exception
     * as an argument. If a controller throws an exception, an error handler
     * can return a specific response.
     *
     * When an exception occurs, all handlers will be called, until one returns
     * something (a string or a Response object), at which point that will be
     * returned to the client.
     *
     * For this reason you should add logging handlers before output handlers.
     *
     * @param mixed $callback Error handler callback, takes an Exception argument
     * @param int   $priority The higher this value, the earlier an event
     *                        listener will be triggered in the chain (defaults to -8)
     */
    public function error($callback, $priority = -8)
    {
        $this->on(KernelEvents::EXCEPTION, new ExceptionListenerWrapper($this, $callback), $priority);
    }

    /**
     * Registers a view handler.
     *
     * View handlers are simple callables which take a controller result and the
     * request as arguments, whenever a controller returns a value that is not
     * an instance of Response. When this occurs, all suitable handlers will be
     * called, until one returns a Response object.
     *
     * @param mixed $callback View handler callback
     * @param int   $priority The higher this value, the earlier an event
     *                        listener will be triggered in the chain (defaults to 0)
     */
    public function view($callback, $priority = 0)
    {
        $this->on(KernelEvents::VIEW, new ViewListenerWrapper($this, $callback), $priority);
    }

    /**
     * Redirects the user to another URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code (302 by default)
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Creates a streaming response.
     *
     * @param mixed $callback A valid PHP callback
     * @param int   $status   The response status code
     * @param array $headers  An array of response headers
     *
     * @return StreamedResponse
     */
    public function stream($callback = null, $status = 200, array $headers = array())
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    /**
     * Escapes a text for HTML.
     *
     * @param string $text         The input text to be escaped
     * @param int    $flags        The flags (@see htmlspecialchars)
     * @param string $charset      The charset
     * @param bool   $doubleEncode Whether to try to avoid double escaping or not
     *
     * @return string Escaped text
     */
    public function escape($text, $flags = ENT_COMPAT, $charset = null, $doubleEncode = true)
    {
        return htmlspecialchars($text, $flags, $charset ?: $this['charset'], $doubleEncode);
    }

    /**
     * Convert some data into a JSON response.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return JsonResponse
     */
    public function json($data = array(), $status = 200, array $headers = array())
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Sends a file.
     *
     * @param \SplFileInfo|string $file               The file to stream
     * @param int                 $status             The response status code
     * @param array               $headers            An array of response headers
     * @param null|string         $contentDisposition The type of Content-Disposition to set automatically with the filename
     *
     * @return BinaryFileResponse
     */
    public function sendFile($file, $status = 200, array $headers = array(), $contentDisposition = null)
    {
        return new BinaryFileResponse($file, $status, $headers, true, $contentDisposition);
    }

    public function mount($prefix, $controllers)
    {
        if ($controllers instanceof Provider\ControllerProviderInterface) {
            $connectedControllers = $controllers->connect($this);

            if (!$connectedControllers instanceof ControllerCollection) {
                throw new \LogicException(sprintf('The method "%s::connect" must return a "ControllerCollection" instance. Got: "%s"', get_class($controllers), is_object($connectedControllers) ? get_class($connectedControllers) : gettype($connectedControllers)));
            }

            $controllers = $connectedControllers;
        } elseif (!$controllers instanceof ControllerCollection && !is_callable($controllers)) {
            throw new \LogicException('The "mount" method takes either a "ControllerCollection" instance, "ControllerProviderInterface" instance, or a callable.');
        }

        $this['controllers']->mount($prefix, $controllers);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        #添加系统事件的监听者、订阅者。以及部分服务的初始化(boot)
        if (!$this->booted) {
            $this->boot();
        }

        $this->flush();

        return $this['kernel']->handle($request, $type, $catch);
    }

    public function flush()
    {
        $this['routes']->addCollection($this['controllers']->flush());
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this['kernel']->terminate($request, $response);
    }

    public function loadconfig($name)
    {
        $file_dir = dirname(dirname(__FILE__))."\\config\\";
        $path = $file_dir.$name.".php";

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Config file '$file_name' not Exists!");
        }

        $parameter = include_once $path;
        if (!is_array($parameter)) {
            throw new \InvalidArgumentException("The config file do not return array file not an array");
        }
        return $parameter;
    }

    public function __call($method, $arguments)
    {
        if (false === strpos($method, 'get')) {
            throw new \BadMethodCallException(sprintf('Method "%s" does not exist.', $method));
        }

        $method = preg_split("/(?=[A-Z])/", $method);

        if(count($method) > 2){
            unset($method[0]);
            $method = implode('_', $method);
        }else{
            $method = $method[1];
        }

        return $this[strtolower($method)];
    }
}