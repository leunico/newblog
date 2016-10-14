<?php  include_once 'adminheader.tpl.php'; ?>


<div class="span12">
		  <div class="row-fluid">
			<div class="page-header">
				<h1>Comments <small>All Comments</small></h1>
			</div>
            <div>
                <form class="form-horizontal" action="<?php echo $view->Route('manage/comments'); ?>" method="get">
				<fieldset>
                  <select name="type" style="width:120px">
                            <option value="">筛选类型</option>
                            <option value="aid" <?php if($scree['type']=='aid') echo "selected";?>>文章ID</option>
                            <option value="contents" <?php if($scree['type']=='contents') echo "selected";?>>评论内容</option>
                            <option value="nickname" <?php if($scree['type']=='nickname') echo "selected";?>>昵称</option>
                  </select>
                  <input type="text" style="width:120px" class="input-xlarge" id="role" name="val" placeholder="要搜索的内容" value="<?php echo $scree['val']; ?>"/>
                  <button type="submit" class="btn btn-success" name="dosubmit" value="dosubmit">筛选</button>
                </fieldset>
			    </form>
            </div>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th>ID</th>
						<th>被评文章</th>
						<th>评论内容</th>
						<th>昵称</th>
                        <th>创建时间</th>
                        <th>个人网址</th>
						<th>评论操作</th>
					</tr>
				</thead>
				<tbody>
                <?php foreach($CommentList as $comment){?>
				<tr class="list-users">
					<td><?php echo $comment['id'];?></td>
                    <td><a href="<?php echo $view->Route('articleshow/'.$comment['aid']);?>" target="_blank"><?php echo $comment['title'] ? $comment['title'] : '文章不存在了！' ?></a></td>
					<td><span><?php echo $comment['contents'];?></span></td>
                    <td><span><?php echo $comment['nickname'];?></span></td>
                    <td><span><?php echo date('Y-m-d H:i:s', $comment['ctime']);?></span></td>
                    <td><?php echo $comment['website'] ? "<a href=http://".$comment['website']." target=\"_blank\">".$comment['website']."</a>":"<span>No Website</span>"; ?></td>
					<td>
						<div class="btn-group">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
							<ul class="dropdown-menu">
                                <li><a href="<?php echo $view->Route('manage/comment_edit/'.$comment['id']);?>"><i class="icon-pencil"></i> Edit</a></li>
								<li><a href="javascript:if(confirm('确定吗？'))window.location='<?php echo $view->Route('manage/comment_delete/'.$comment['id']);?>'"><i class="icon-trash"></i> Delete</a></li>
								<li class="nav-header">Hello Alice</li>
							</ul>
						</div>
					</td>
				</tr>
                <?php }?>
				</tbody>
			</table>
            <?php echo $pageNav ?>
		  </div>
        </div>
      </div>



<?php  include_once 'adminfooter.tpl.php'; ?>