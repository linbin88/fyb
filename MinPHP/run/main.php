<?php defined('API') or exit('http://gwalker.cn');?>
<!DOCTYPE html>
<html lang="zh-CN" style="height:100%">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>FYB信息管理</title>
    <link rel="icon" type="image/x-icon" href="./MinPHP/res/favicon.ico">
    <link href="./MinPHP/res/bootstrap-3.3.4-dist/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="./MinPHP/res/html5shiv.min.js"></script>
    <script src="./MinPHP/res/respond.min.js"></script>
    <![endif]-->
<script src="./MinPHP/res/jquery.min.js"></script>

<script src="./MinPHP/res/bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
    
    <link href="./MinPHP/res/style.css" rel="stylesheet" type="text/css"/>
</head>
<body style="height:100%">
<div class="container-fluid" style="background:white;height:100%;">
    <div class="row" style="height:100%;">
        <!--左侧导航start-->
        <div class="col-md-3" style="position:relative;background:#f5f5f5;padding:10px;height:100%;border-right:#ddd 1px solid;overflow-y:auto">
            <div style="height:50px;font-size:30px;line-height:50px;">
                <a style="color:#000000;text-shadow:1px 0px 1px #666;text-decoration: none" href="<?php echo U()?>">
                    <span class="" aria-hidden="true" style="width:40px;"</span>
        <span style="position: relative;top:-3px;"><?php echo C('version->name')?><span style="font-size:12px;position:relative;top:-13px;">&nbsp;<?php echo C('version->no')?></span>
                </a>
                </span>
            </div>
            <?php
            include('./MinPHP/run/menu.php');
            ?>
        </div>
        <!--左侧导航end-->
        <div class="col-md-9" style="height:100%;background:white;margin:0px;overflow-y:auto;padding:0px;">
            <!--顶部导航start-->
            <div class="textshadow" style="font-size:16px;widht:100%;height:60px;line-height:60px;padding:0 16px 0 16px;;border-bottom:#ddd 1px solid">
                <span> <a href="<?php echo U() ?>">Home</a><?php echo $menu;?></span>
        <span style="float:right">
            <?php
            if(is_lgoin()){
                if(is_supper()){
                    echo '<a href="JavaScript:void(0);" class="bak-sql">手动备份&nbsp;&nbsp;</a>';
                }
                echo '<a href="JavaScript:void(0);" class="edit-password">修改密码&nbsp;&nbsp;</a>';
                echo '<a href="?act=login&type=quit">退出&nbsp;&nbsp;<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a>';
            }else{
                echo '<a href="?act=login">登录&nbsp;&nbsp;<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></a>';
            }
            ?>
        </span>
            </div>
            <!--顶部导航end-->
            <!--主窗口start-->
            <div style="padding:16px;">
                <?php
                if(!empty($file)){
                    include($file);
                }
                ?>
            </div>
            <!--主窗口end-->
        </div>
    </div>
</div>
<script>
$(".edit-password").click(function(){
    var person=prompt("请输入你的新密码","123456");
    if (person!=null && person!="")
    {
        alert(person);
    }

})
<?php
    $db_bak=end(scandir('./db_bak'));
    $db_bak=str_ireplace('api','',$db_bak);
    $db_bak=str_ireplace('.sql','',$db_bak);
?>
$(".bak-sql").click(function(){
    if (confirm("上次备份时间是：<?php echo date("Y-m-d H:i:s",strtotime($db_bak)); ?>,确定再次备份吗？备份会保留5天")) {
        window.location.href="index.php?b=1";
    }else{

    }
    
})
</script>
</body>
</html>