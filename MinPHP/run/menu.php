<?php defined('API') or exit('http://gwalker.cn');?>
<!--导航-->

<?php 
if (is_lgoin()) {
    if (is_supper()) {
        $one_list = select('select * from cate where isdel=0 order by addtime desc');
    }else{//普通账号
        $one_list = select('select * from cate where isdel=0 AND aid in(select aid from auth where uid='.intval(session('id')).') order by addtime desc');
    }
}else{
    return 1;
    $one_list=array();
}
$act='api';
// $_GET['tag']=isset($_GET['tag'])?intval($_GET['tag']):$one_list[0]['aid'];
if($act != 'api'){
    if (is_lgoin()) {
        if (is_supper()) {
            $one_list = select('select * from cate where isdel=0 order by addtime desc');
        }else{//普通账号
            $one_list = select('select * from cate where isdel=0 AND aid in(select aid from auth where uid='.intval(session('id')).') order by addtime desc');
        }
    }else{
        $one_list=array();
    }
?>
    <div class="form-group">
        <input type="text" class="form-control" id="searchcate" onkeyup="search('cate',this)" placeholder="search here">
    </div>
    <div class="list">
        <ul class="list-unstyled">
            <?php foreach($one_list as $v){?>
            <form action="?act=cate" method="post">
            <li class="menu" id="info_<?php echo $v['aid'];?>">
                <a href="<?php echo U(array('act'=>'api','tag'=>$v['aid']))?>">
                    <?php echo $v['cname']?>
                </a>
                <br>
                <?php echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$v['cdesc'];echo "<input type='hidden' name='aid' value='{$v['aid']}'>";?>
                <br>
                <?php if(is_supper()){?>
                <!--只有超级管理员才可以对分类进行操作-->
                <div style="float:right;margin-right:16px;">
                    &nbsp;<button class="btn btn-danger btn-xs" name="op" value="delete" onclick="javascript:return confirm('您确认要删除吗?')">delete</button>
                    &nbsp;<button class="btn btn-info btn-xs" name="op" value="edit">edit</button>
                </div>
                <br>
                <?php } ?>
                <hr>
            </li>
            <!--接口分类关键字(js通过此关健字进行模糊查找)start-->
            <span class="keyword" id="<?php echo $v['aid'];?>"><?php echo $v['cdesc'].'<|-|>'.$v['cname'];?></span>
                <!--接口关键字(js通过此关健字进行模糊查找)end-->
            </form>
            <?php } ?>
        </ul>
    </div>

    <form action="?act=cate" method="post">
        <?php if(is_supper()){?>
        <!--只有超级管理员才可以添加分类-->
        <div style="float:right;margin-right:20px;">
            <button class="btn btn-success" name="op" value="add">新建分类</button>
        </div>
        <?php } ?>
    </form>
<?php } else{
    // if (!is_supper()) {//普通账号
    //     $list = select('select aid from auth where uid='.intval(session('id')));
    //     $i=0;
    //     foreach ($list as $key => $value) {
    //         if ($value['aid']==$_GET['tag']) {
    //             $i=1;
    //         }
    //     }
    //     if (!$i) {
    //         // go(U());
    //     }
    // }
    $sql = "select * from api where  isdel='0' order by ord desc,id desc";
    $list = select($sql);
    $sql = "SELECT * FROM  `group`  order by sort desc,id desc";
    $group = select($sql);
    $aid=array();
    foreach ($group as $key => $value) {
        if (!in_array($value['aid'],$aid)) {
            $aid[]=$value['aid'];
            $group[]=array(
                'id'=>0,
                'aid'=>$value['aid'],
                'name'=>'其他',
                'info'=>'',
            );
        }
    }

    // if($group){
    //     $group[]=array(
    //         'id'=>0,
    //         'aid'=>$_GET['aid'],
    //         'name'=>'其他',
    //         'info'=>'',
    //     );
    // }
    foreach ($list as $key => $value) {
        foreach ($group as $key1 => $value1) {
            if ($value['gid']==$value1['id'] && $value['aid']==$value1['aid']) {
                $group[$key1]['list'][]=$value;
            }
        }
    }
    foreach ($one_list as $key1 => $value1) {
        $one_list[$key1]['list']=array();
        foreach ($group as $key => $value) {
            if ($value['aid']==$value1['aid']) {
                $one_list[$key1]['list'][]=$value;
            }
        }
    }
    // echo '<pre>';
    // print_r($one_list);
    // print_r($group);
?>
    <div class="form-group">
        <input type="text" class="form-control" id="searchapi" placeholder="search here" onkeyup="search('api',this)">
    </div>
    <div class="list">
        <?php if(is_supper()){?>
        <div>
            <!-- <span class="btn btn-xs btn-info" style="float:right;margin-right:30px;" data-toggle="modal" data-target="#myModald">导出项目</span> -->
            <span class="btn btn-xs btn-success add-group" style="float:right;margin-right:30px;" data-toggle="modal" data-target="#myModal">新建项目</span><br>
        </div>
        <?php } ?>
        <ul class="list-unstyled" style="padding:10px">
            <?php
                foreach ($one_list as $key => $value) {
                    // if ($value['aid']==$_GET['tag']) {
                        //当前分类
            ?>
                <li class="menu " title="<?php echo $value['cname']?'('.$value['cname'].')':''; ?>" data-id="<?php echo $value['aid'];?>" style="color: #010165;cursor: pointer;"  >
                        <span class="glyphicon glyphicon-menu-right oall" data-id="<?php echo $value['aid'];?>" aria-hidden="true"><?php echo $value['cname'] ?></span>
                        &nbsp;<span class="badge"><?php echo count($value['list']); ?></span>
                        <?php
                            if (is_supper() && $value['aid']>0) {
                                echo '<div style="float:right;margin-right:16px;">
                                    <span class="glyphicon glyphicon-remove delete-one-group" style="color:#c9302c" data-id='.$value['aid'].'></span>
                                    &nbsp;<span class="glyphicon glyphicon-edit edit-one-group" style="color:#31b0d5" data-toggle="modal" data-target="#myModaleonedit" data-id="'.$value['aid'].'" data-name="'.$value['cname'].'" data-info="'.$value['info'].'" data-sort="'.$value['sort'].'" ></span>
                                    &nbsp;<span class="glyphicon glyphicon-plus add-one-group" style="color:#398439" data-toggle="modal" data-target="#myModaladd" data-id="'.$value['aid'].'"></span>
                                </div>';
                            }
                        ?>
                </li>
                <?php foreach($value['list'] as $v1){ ?>
                    <li class="menu mall mall-<?php echo $value['aid']; ?>" title="<?php echo $v1['info']?'('.$v1['info'].')':''; ?>" data-id="<?php echo $v1['id'];?>" style="display: none;color: #010165;cursor: pointer;margin-left:15px;"  >
                        <span class=" glyphicon glyphicon-menu-right" aria-hidden="true"><?php echo $v1['name'] ?></span>
                        &nbsp;<span class="badge"><?php echo count($v1['list']); ?></span>
                        <?php
                            if (is_supper() && $v1['id']>0) {
                                echo '<div style="float:right;margin-right:16px;">
                                    &nbsp;<span class="glyphicon glyphicon-remove delete-group" style="color:#c9302c" data-id='.$v1['id'].'></span>
                                    &nbsp;<span class="glyphicon glyphicon-edit edit-group" style="color:#31b0d5" data-toggle="modal" data-target="#myModaledit" data-id="'.$v1['id'].'" data-name="'.$v1['name'].'" data-info="'.$v1['info'].'" data-sort="'.$v1['sort'].'" ></span>
                                     &nbsp;<a class="glyphicon glyphicon-plus add-api-group" style="color:#398439" href="?act=api&tag='.$value['aid'].'&gid='.$v1['id'].'&op=add" ></a>
                                </div>';
                            }
                        ?>
                    </li>
                    <?php foreach($v1['list'] as $v){ ?>
                    <li class="menu m-<?php echo $v1['id'];?>" style="display:none"  id="api_<?php echo md5($v['id']);?>" >
                        <a class="menu-info" data-agid="<?php echo $value['aid'].'-'.$v1['id']; ?>" href="<?php echo U(array('act'=>'api')); ?>#info_api_<?php echo md5($v['id']) ?>" id="<?php echo 'menu_'.md5($v['id'])?>">
                           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                           <span class="glyphicon glyphicon-info-sign" style="    top: 2.5px;right: 2px;" ></span><?php echo $v['name'] ?>
                            
                        </a>
                    </li>
                    <!--接口关键字(js通过此关健字进行模糊查找)start-->
                    <span class="keyword" id="<?php echo md5($v['id'])?>"><?php echo $v['name'].'<|-|>'.$v['num'].'<|-|>'.$v['des'].'<|-|>'.$v['memo'].'<|-|>'.$v['parameter'].'<|-|>'.$v['url'].'<|-|>'.$v['type'].'<|-|>'.strtolower($v['type']);?></span>
                    <!--接口关键字(js通过此关健字进行模糊查找)end-->
                    <?php } ?>
                <?php } ?>


            <?php
                    // }else{
            ?>
              
            <?php
                    // }
                }
            ?>
            
        </ul>
    </div>
   
<?php } ?>
<!-- 模态框（编辑分类） -->
<div class="modal fade" id="myModaleonedit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="?act=cate&type=do" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        编辑分类
                    </h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" name="name" placeholder="请输入名称">
                    <!-- <input class="form-control" name="info" placeholder="请输入描述"> -->
                    <br>
                    <input class="form-control" name="sort" type="number" placeholder="输入排序默认99" value="99">
                    <input class="form-control" type="hidden" name="op" value="edit">
                    <input class="form-control" type="hidden" name="aid" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit"  class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal -->
</div>
<!-- 模态框（添加分类） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="?act=cate&type=do" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        建立新的项目
                    </h4>
                </div>

                <div class="modal-body">
                    <input type="text" class="form-control" name="cname" placeholder="项目名">
                    <br>
                    <input type="text" class="form-control" name="cdesc" placeholder="描述">
                    <br>
                    <input class="form-control" name="sort" type="number" placeholder="输入排序默认99" value="99">
                    <input class="form-control" type="hidden" name="op" value="add">
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
<!-- 模态框（导出项目） -->
<div class="modal fade" id="myModald" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="?act=index&op=download" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        导出项目
                    </h4>
                </div>
                <div class="modal-body">
                   <div class="form-group" required="required">
                        <h5>选择导出的数据库<span style="font-size:12px;padding-left:20px;color:#a94442">&nbsp;Control+鼠标点击可以多选</span></h5>
                        <select multiple="" class="form-control" name="data[]">
                            <?php
                                foreach ($one_list as $key => $value) {
                                    echo '<optgroup label="'.htmlspecialchars($value['cname']).'"></optgroup>';
                                    foreach ($value['list'] as $key1 => $value1) {
                                        echo '<option value="'.intval($value1['aid']).'-'.intval($value1['id']).'">'.htmlspecialchars($value1['name']).'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
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
<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" class="form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        编辑数据库
                    </h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" name="name" placeholder="请输入名称">
                    <input class="form-control" name="sort" type="number" placeholder="输入排序默认99" value="99">
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

<!-- 模态框（添加二级分类） -->
<div class="modal fade" id="myModaladd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" id="myModalLabel-one-add" class="form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" >
                        添加分类
                    </h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" name="name" placeholder="请输入名称">
                    <br>
                    <input class="form-control" name="sort" type="number" placeholder="输入排序默认99" value="99">
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
<!--jquery模糊查询start-->
<script type="text/javascript">
        function search(type,obj){
            var $find = $.trim($(obj).val());//得到搜索内容
            if(type == 'cate'){//对接口分类进行搜索操作
                if($find != ''){
                    $(".menu").hide();
                    //找到符合关键字的对象
                    var $keywordobj = $(".keyword:contains('"+$find+"')")
                    $keywordobj.each(function(i) {
                        var menu_id = $($keywordobj[i]).attr('id');
                        $("#info_"+menu_id).show();
                    });
                }else{
                    $(".menu").show();//在没有搜索内容的情况下,左侧导航菜单 全部 显示
                }
            }else if(type == 'api'){//对接口进行搜索操作
                if($find != ''){
                    $(".menu").hide();//左侧导航菜单隐藏
                    $(".info_api").hide();
                    //找到符合关键字的对象
                    var $keywordobj = $(".keyword:contains('"+$find+"')")
                    $keywordobj.each(function(i) {
                        var menu_id = $($keywordobj[i]).attr('id');
                        $("#api_"+menu_id).show();//左侧导航菜单 部份 隐藏
                        $("#info_api_"+menu_id).show();//接口详情 部份 隐藏
                    });
                }else{
                    $(".menu").show();//在没有搜索内容的情况下,左侧导航菜单 全部 显示
                    $(".info_api").show();//在没有搜索内容的情况下,接口详情 全部 显示
                }
            }
        }
        
        $(".delete-one-group").click(function(){
            //删除分类
            var id=$(this).attr('data-id');
            if (confirm('您确认要删除吗?,删除后将无法自动恢复')) {
                　window.location.href="./index.php?act=cate&op=delete&aid="+id;
            }
        })
        $(".add-one-group").click(function(){
            //添加分类
            var id=$(this).attr('data-id')
            $("#myModalLabel-one-add").attr('action','?act=api&tag='+id+'&op=addgroup')
        })
        $(".edit-one-group").click(function(){
            //编辑分类
            var id=$(this).attr('data-id');
            var name=$(this).attr('data-name');
            var sort=$(this).attr('data-sort');
            $("#myModaleonedit").find("input[name='name']").attr("value",name);
            $("#myModaleonedit").find("input[name='aid']").attr("value",id);
            $("#myModaleonedit").find("input[name='sort']").attr("value",sort);
        })
        $(".oall").click(function(){
            var aid=$(this).attr('data-id')
            if ($(this).find(".glyphicon").hasClass('glyphicon-menu-right')) {
                $(this).find(".glyphicon").removeClass('glyphicon-menu-right')
                $(this).find(".glyphicon").addClass('glyphicon-menu-down')
            }else{
                $(this).find(".glyphicon").removeClass('glyphicon-menu-down')
                $(this).find(".glyphicon").addClass('glyphicon-menu-right')
            }
            $(".mall-"+aid).each(function(){
                if (!$(this).find(".glyphicon").hasClass('glyphicon-menu-right')) {
                    $(this).click()
                }
            });
            $(".mall-"+aid).toggle(200);
            
        })
        $(".mall").click(function(){
            var id=$(this).attr('data-id');
            $(".m-"+id).toggle(200);
            if ($(this).find(".glyphicon").eq(0).hasClass('glyphicon-menu-right')) {
                $(this).find(".glyphicon").eq(0).removeClass('glyphicon-menu-right')
                $(this).find(".glyphicon").eq(0).addClass('glyphicon-menu-down')
            }else{
                $(this).find(".glyphicon").eq(0).removeClass('glyphicon-menu-down')
                $(this).find(".glyphicon").eq(0).addClass('glyphicon-menu-right')
            }
            
        })
        $(".delete-group").click(function(){
            var id=$(this).attr('data-id');
            if (confirm('您确认要删除吗?,删除后数据库下的接口将会变成其他数据库')) {
                　window.location.href="./index.php?act=api&op=deletegroup&group="+id;
            }
        })
        $(".edit-group").click(function(){
            var id=$(this).attr('data-id');
            var name=$(this).attr('data-name');
            var info=$(this).attr('data-info');
            var sort=$(this).attr('data-sort');
            $("#myModaledit").find("form").attr("action","?act=api&op=editgroup&group="+id);
            $("#myModaledit").find("input[name='name']").attr("value",name);
            // $("#myModaledit").find("input[name='info']").attr("value",info);
            $("#myModaledit").find("input[name='sort']").attr("value",sort);
        })
        $(function(){
            $(".menu-info").click(function(){
                var agid=$(this).attr('data-agid');
                // alert(agid)
                $(".info_api").hide();
                $(".group-"+agid).show();

            })
            try
            {
                //自动展开菜单程序
                setTimeout(function(){
                    var query = location.href.split('#');
                    console.log(query)
                    if (query.length>1) {
                        var query = query[1].split('_'); 
                        var idmd5=query[2]
                        console.log(idmd5)
                        var clas=$("#api_"+idmd5).attr('class');//.find("li").eq(0);//.click()
                        var clas = clas.split(' '); 
                        var clas = clas[1].split('-'); 
                        console.log(clas[1])
                        var p1=$("[data-id='"+clas[1]+"']");
                        p1.each(function(){
                            if ($(this).is('.mall')) {
                                p1=$(this)
                            }
                        })
                        var clas=$(p1).attr('class');//.find("li").eq(0);//.click()
                        var clas = clas.split(' '); 
                        console.log(clas)
                        var clas = clas[2].split('-'); 
                        var p2=$("[data-id='"+clas[1]+"']");
                        p2.each(function(){
                            if ($(this).is('.oall')) {
                                // p2=$(this)
                            }
                        })
                        console.log(clas[1])
                            console.log(p2)
                            console.log(p1)


                        p2.eq(0).find("span").eq(0).click();
                        p1.eq(0).find("span").eq(0).click();
                    }
                },200)
                

            }
            catch(err)
            {
               //在此处理错误
            }
            
        })
</script>
<!--jquery模糊查询end-->
<!--end-->