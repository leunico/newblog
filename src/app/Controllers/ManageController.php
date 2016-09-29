<?php
namespace app\Controllers;

class ManageController extends Controller
{
    public function index()
    {
        return $this->render('admin/index', $this->parameters);
    }

}
