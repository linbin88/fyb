<?php defined('API') or exit('http://gwalker.cn');?>
<!--欢迎页-->
<!--info start-->
<div style="font-size:18px;">
    <span style="font-size:26px;" class="glyphicon glyphicon-grain" aria-hidden="true"></span> 欢迎使用FYB信息管理 <?php echo C('version->no').'版';?>
</div>
<?php
	

	if (is_supper()) {
		$_VAL = I($_POST);
	   //操作类型{add,delete,edit}
	   $op = $_GET['op'];
	   //添加接口
	   if($op == 'adduser'){
	        $login_name = $_VAL['login_name'];  //接口名称
	        $login_pwd = $_VAL['login_pwd']; //备注
	        $nice_name = $_VAL['nice_name'];    //描述
	        $issuper = $_VAL['issuper'];  //请求方式
	        $aid = $_VAL['aid'];
	      
	        $sql = "INSERT INTO `user` (`id`, `nice_name`, `login_name`, `last_time`, `login_pwd`, `isdel`, `issuper`) VALUES (NULL, '{$nice_name}', '{$login_name}', '0', '{$login_pwd}', '0', '{$issuper}');";
	        $re = insert($sql);
            if($re){
            	if (!$issuper && $aid) {
            		$uid=select("SELECT id FROM user WHERE login_name='".$login_name."'");
            		if ($uid) {
            			$uid=$uid[0]['id'];
            			foreach ($aid as $key => $value) {
            				$sql = "INSERT INTO `auth` (`id`, `uid`, `aid`) VALUES (NULL, '{$uid}', '{$value}');";
	        				insert($sql);
            			}
            		}
            	}
                go(U(array('index'=>'api','tag'=>$_GET['tag'])));
            }else{
                echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 添加失败</div>';
            }
	   	}elseif ($op=='deleteuser') {
	   		
	   		$id = $_GET['id'];
	   		if($id==1){
	   			exit();
	   		}
   		  	$sql = "DELETE FROM `user` WHERE `id`=".$id;
        	$re = update($sql);
        	if ($re){
			  	$sql = "DELETE FROM `auth` WHERE `uid`=".$id;
		     	$re = update($sql);
        		 go(U(array('index'=>'index','tag'=>$_GET['tag'])));
            }else{
                echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>删除失败</div>';
        	}
	   	}elseif($op=='edituser'){//修改权限
            $id = $_GET['uid'];
            if($id==1){
                exit();
            }
            $sql = "DELETE FROM `auth` WHERE `uid`=".$id;
            update($sql);
            $aid = $_VAL['aid'];
            foreach ($aid as $key => $value) {
                $sql = "INSERT INTO `auth` (`id`, `uid`, `aid`) VALUES (NULL, '{$id}', '{$value}');";
                insert($sql);
            }
             go(U(array('index'=>'index','tag'=>$_GET['tag'])));
        }
		$user=select("SELECT * FROM user where isdel=0");
		$cate=select("SELECT * FROM cate where isdel=0");
		$auth=select("SELECT * FROM auth");
?>
	<table class="table table-bordered">
	  <caption><button class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModaladduser">添加用户</button></caption>
	  <thead>
	    <tr>
	      <th>账号</th>
	      <th>姓名</th>
	      <th>权限</th>
	      <th>上次登录时间</th>
	      <th>操作</th>
	    </tr>
	  </thead>
	  <tbody>
	  	<?php
	  		foreach ($user as $key => $value) {
	  			$issuper='';
	  			if (!$value['issuper']) {
                    $aids="";
                    $uid=$value['id'];
  					foreach ($auth as $key2 => $value2) {
  						if ($value2['uid']!=$value['id']) {
  							continue;
  						}
	  					foreach ($cate as $key1 => $value1) {
	  						if ($value2['uid']==$value['id'] && $value1['aid']==$value2['aid']) {
	  							$issuper.='<lable class="label label-success">'.$value1['cname'].'</lable>&nbsp;';
                                $aids.=$value2['aid'].',';
	  						}
	  					}
	  				}
                    $issuper.='<button class="btn btn-info btn-xs edit-auth" data-toggle="modal" data-target="#myModaleditauth" data-auth="'.$aids.'" data-id="'.$uid.'">edit</button>';
	  			}else{
	  				$issuper='管理员';

	  			}
	  	?>
	  	<tr>
	  	  <td><?php echo $value['login_name']; ?></td>
	  	  <td><?php echo $value['nice_name'];?></td>
	  	  <td><?php echo $issuper;?></td>
	  	  <td><?php echo date("Y-m-d H:i:s",$value['last_time']); ?></td>
	  	  <td>
	  	  	<?php if($value['id']!=1){?> 
	  	  		<button class="btn btn-danger btn-xs delete-user" data-id="<?php echo $value['id']; ?>">删除</button>
	  	  		<?php } ?>
	  	  	</td>
	  	</tr>
	  	<?php
	  		}
	  	?>
	  </tbody>
	</table>


    <!-- 模态框（Modal） -->
<div class="modal fade" id="myModaladduser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="?act=index&op=adduser" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        添加用户
                    </h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" name="login_name" placeholder="账号">
                    <br>
                    <input class="form-control" name="login_pwd" placeholder="密码">
                    <br>
                    <input class="form-control" name="nice_name" placeholder="姓名">
                    <br>
                    <label for="name">权限</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="issuper" id="optionsRadios1" value="1" checked>管理员
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="issuper" id="optionsRadios2" value="0">用户
                        </label>
                    </div>
                    <label for="name">用户-权限</label>
                    <select multiple class="form-control" name="aid[]">
                        <?php
                            foreach ($cate as $key => $value) {
                                echo '<option value="'.$value['aid'].'">'.$value['cname'].'</option>';
                                
                            }
                        ?>
                    </select>          
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal -->
</div>

    <!-- 模态框（Modal） -->
<div class="modal fade" id="myModaleditauth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="?act=index&op=edituser&uid=" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        修改权限
                    </h4>
                </div>
                <div class="modal-body">
                    <label for="name">用户-权限</label>
                    <select multiple class="form-control" name="aid[]">
                        <?php
                            foreach ($cate as $key => $value) {
                                echo '<option value="'.$value['aid'].'">'.$value['cname'].'</option>';
                            }
                        ?>
                    </select>          
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal -->
</div>
<script>
    window.onload=function (){
        $(".delete-user").click(function(){
            var id=$(this).attr('data-id');
            if (confirm('您确认要删除吗?,')) {
                　window.location.href="./index.php?act=index&op=deleteuser&id="+id;
            }
        })
        $(".edit-auth").click(function(){
            var uid=$(this).attr('data-id');
            var auth=$(this).attr('data-auth');
            $("#myModaleditauth").find('form').attr("action",'?act=index&op=edituser&uid='+uid)
        })
    }
    
</script>
<?php
	}

?>

<!--欢迎页 end-->