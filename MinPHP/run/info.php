<?php defined('API') or exit();?>
<!--接口详情列表与接口管理start-->
<?php
   $_VAL = I($_POST);
   //操作类型{add,delete,edit}
   $op = $_GET['op'];
   $type = $_GET['type'];
   //添加接口
   if($op == 'add'){
        if($type == 'do'){
            if(!is_supper()){die('只有超级管理员才可对接口进行操作');}
            $aid = I($_GET['tag']);    //所属分类
            if(empty($aid)){
                die('<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 所属分类不能为空');
            }
            $num = $_VAL['num'];   //接口编号
            $name = $_VAL['name'];  //接口名称
            $memo = $_VAL['memo']; //备注
            $des = $_VAL['des'];    //描述
            $gid = $_VAL['gid'];
            $ord = $_VAL['ord'];
            $url = $_VAL['url'];
            $type = $_VAL['type'];
            $parameter = serialize($_VAL['p']);
            $re = $_VAL['re'];  //返回值
            $lasttime = time(); //最后操作时间
            $lastuid = session('id'); //操作者id
            $isdel = 0; //是否删除的标识
            $sql = "insert into api (
            `aid`,`num`,`name`,`des`,
            `parameter`,`lasttime`,
            `lastuid`,`isdel`,`memo`,`gid`,`url`,`type`,`ord`,`re`
            )values (
            '{$aid}','{$num}','{$name}','{$des}','{$parameter}','{$lasttime}',
            '{$lastuid}','{$isdel}','{$memo}','{$gid}','{$url}','{$type}','{$ord}','{$re}'
            )";
            $re = insert($sql);
            if($re){
                go(U(array('act'=>'api','tag'=>$_GET['tag'])));
            }else{
                echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 添加失败</div>';
            }
        }
   //修改接口
   }else if($op == 'edit'){
       if(!is_supper()){die('只有超级管理员才可对接口进行操作');}
       //执行编辑
       if($type == 'do'){

            $id = $_VAL['id'];   //接口id
            $num = $_VAL['num'];   //接口编号
            $name = $_VAL['name'];  //接口名称
            $memo = $_VAL['memo']; //备注
            $des = $_VAL['des'];    //描述
            $gid = $_VAL['gid'];
            $ord = $_VAL['ord'];
            $url = $_VAL['url'];
            $type = $_VAL['type'];
            $parameter = serialize($_VAL['p']);
            $re = $_VAL['re'];  //返回值
            $lasttime = time(); //最后操作时间
            $lastuid = session('id'); //操作者id

           echo $sql ="update api set num='{$num}',des='{$des}',name='{$name}',
           gid='{$gid}',ord='{$ord}',other_api='{$other_api}',
           parameter='{$parameter}',url='{$url}',lastuid='{$lastuid}',memo='{$memo}',type='{$type}',re='{$re}'
           where id = '{$id}'";
           $re = update($sql);
           if($re){
               go(U(array('act'=>'api','tag'=>($_GET['tag'].'#info_api_'.md5($id)))));
           }else{
               echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 修改失败</div>';
           }
       }
       //编辑界面
       if(empty($id)){$id = I($_GET['id']);}
       $aid = I($_GET['tag']);
       //得到数据的详情信息start
       $sql = "select * from api where id='{$id}'";
       $info = find($sql);
       //得到数据的详情信息end
       if(!empty($info)){
           $info['parameter'] = unserialize($info['parameter']);
           $count = count($info['parameter']['name']);
           $p = array();
           for($i = 0;$i < $count; $i++){
               $p[$i]['name']=$info['parameter']['name'][$i];
               $p[$i]['type']=$info['parameter']['type'][$i];
               $p[$i]['default']=$info['parameter']['default'][$i];
               $p[$i]['des']=$info['parameter']['des'][$i];
           }
           $info['parameter'] = $info['parameter'];
       }
   //此分类下的接口列表
   }else if($op=='addgroup'){
        if (!is_supper()) {
            exit;
        }
        $aid = I($_GET['tag']);    //所属分类
        if(empty($aid)){
            die('<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 所属分类不能为空');
        }
        $name = $_VAL['name'];  //接口名称
        $info = $_VAL['info']; //备注
        $sort = intval($_VAL['sort']);    //描述
        $sql = "INSERT INTO `group` (`id`, `aid`, `name`, `info`, `sort`) VALUES (NULL, '{$aid}', '{$name}', '{$info}', '{$sort}');";
        $re = insert($sql);
        if($re){
            go(U(array('act'=>'api','tag'=>$_GET['tag'])));
        }else{
            echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 添加失败</div>';
        }
   }else if($op=='editgroup'){
        if (!is_supper()) {
            exit;
        }
        // $aid = I($_GET['tag']);    //所属分类
        $group = I($_GET['group']);    //所属分类
        if(empty($aid)){
            die('<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 所属分类不能为空');
        }
        $name = $_VAL['name'];  //接口名称
        $info = $_VAL['info']; //备注
        $sort = intval($_VAL['sort']);    //描述
        $sql = "UPDATE  `group` SET  `name` =  '{$name}', `info` =  '{$info}', `sort` =  '{$sort}' WHERE  `group`.`id` ={$group};";
        $re = insert($sql);
        if($re){
            go(U(array('act'=>'api','tag'=>$_GET['tag'])));
        }else{
            echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 添加失败</div>';
        }
   }else if($op=='deletegroup'){
        if (!is_supper()) {
            exit;
        }
        $group = I($_GET['group']);    //所属分类
        $sql = "DELETE FROM `group` WHERE `id`=".$group;
        $re = update($sql);
        $re = update('update api set gid=0 where gid='.$group);
        if(1){
            go(U(array('act'=>'api','tag'=>$_GET['tag'])));
        }else{
            echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 失败</div>';
        }
   }else{
        $sql = "select api.id,aid,ord,des,num,gid,other_api,name,parameter,memo,lasttime,lastuid,login_name,nice_name,type,url,re
        from api
        left join user
        on api.lastuid=user.id
        where api.isdel=0
        order by ord desc,api.id desc";
        $list = select($sql);
   }
?>
<?php
    $one_list = select('select * from cate where isdel=0 order by addtime desc');

    $sql = "select * from api where  isdel='0' order by ord desc,id desc";
    $list_temp = select($sql);
    $sql = "SELECT * FROM  `group` order by sort desc,id desc";
    $group = select($sql);
    foreach ($one_list as $key => $value) {
        $group[]=array(
            'id'=>0,
            'aid'=>$value['aid'],
            'name'=>'其他',
            'info'=>'',
        );
    }
    foreach ($list_temp as $key => $value) {
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
    // echo '<pre>;';
    // print_r($one_list);
?>
<?php if($op == 'add'){ ?>
    <?php 
        $aid = I($_GET['tag']);    //所属分类
        $group_list=select("SELECT * FROM  `group` WHERE aid=".$aid);
        $maxId=1;
        foreach ($list as $key => $value) {
            if ($aid==$value['aid'] && $value['id']>=$maxId) {
                $maxId=$value['id']+1;
            }
        }
        
    ?>
    <!--添加接口 start-->
    <div style="border:1px solid #ddd">
        <div style="background:#f5f5f5;padding:20px;position:relative">
            <h4>添加数据表<span style="font-size:12px;padding-left:20px;color:#a94442"><!-- 注:"此色"边框为必填项 --></span></h4>
            <div style="margin-left:20px;">
                <form action="?act=api&tag=<?php echo $_GET['tag']?>&type=do&op=add" method="post">
                    <div>
                        <div class="col-md-3 form-group has-error" style="padding-left: 0px;">
                            <div class="input-group">
                                <div class="input-group-addon">接口名称</div>
                                <input type="text" class="form-control" name="name" placeholder="获取用户信息" required="required">
                            </div>
                        </div>
                         <div class="col-md-3 form-group" required="required">
                            <div class="input-group">
                            <div class="input-group-addon">所属分组</div>
                                <select class="form-control" name="gid">
                                    <option value="0">其他分组</option>
                                    <?php
                                        foreach ($group_list as $key => $value) {
                                            $s='';
                                            if ($_GET['gid']==$value['id']) {
                                                $s='selected=""';
                                            }
                                            echo '<option value="'.$value['id'].'" '.$s.'>'.$value['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                         <div class="col-md-3 form-group">
                            <div class="input-group">
                                <div class="input-group-addon">接口编号</div>
                                <input type="text" class="form-control" name="num" placeholder="接口编号" value="<?php echo I($_GET['tag']).'-'.I($_GET['gid']).'-'.$maxId; ?>" required="required">
                            </div>
                        </div>
                         <div class="col-md-3 form-group">
                            <div class="input-group">
                                <div class="input-group-addon">排序</div>
                                <input type="text" class="form-control" name="ord" placeholder="排序" value="99">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-4 form-group" style="padding-left: 0px;">
                            <div class="form-group has-error" >
                                <div class="input-group">
                                    <div class="input-group-addon">请求地址</div>
                                    <input type="text" class="form-control" name="url" placeholder="/user/getUserInfo" required="required">
                                </div>
                            </div>
                            <div class="form-group" >
                            <div class="input-group">
                                <div class="input-group-addon">请求方式</div>
                                <select class="form-control" name="type">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-8 form-group">
                            <div class="input-group">
                                <div class="input-group-addon" style="height: 80px;">数据接口说明</div>
                                <textarea name="des" style="height: 80px;" class="form-control" placeholder="接口说明"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="col-md-3">请求参数名</th>
                                <th class="col-md-2">必传</th>
                                <th class="col-md-2">缺省值</th>
                                <th class="col-md-4">描述</th>
                                <th class="col-md-1">
                                    <button type="button" class="btn btn-success" onclick="add1()">新增</button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="parameter1">
                            <tr>
                                <td class="form-group has-error">
                                    <input type="text" class="form-control" name="p[name][]" placeholder="参数名称" value="">
                                </td>
                                <td>
                                    <select class="form-control" name="p[type][]">
                                        <option value="Y">是</option>
                                        <option value="N">否</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="p[default][]" value="" placeholder="默认值"></td>
                                <td><input type="text" class="form-control" name="p[des][]" value="" placeholder="描述"></td>
                                <td><button type="button" class="btn btn-danger" onclick="del(this)">删除</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <h5>返回结果</h5>
                        <textarea name="re" rows="2" class="form-control" placeholder="返回结果"></textarea>
                    </div>
                    <div class="form-group">
                        <h5>备注</h5>
                        <textarea name="memo" rows="2" class="form-control" placeholder="备注"></textarea>
                    </div>
                    <button class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function add1(){
            var $html ='<tr>'
                +'<td class="form-group has-error">'
                    +'<input type="text" class="form-control" name="p[name][]" placeholder="参数名称" value="">'
                +'</td>'
                +'<td>'
                    +'<select class="form-control" name="p[type][]">'
                        +'<option value="Y">是</option>'
                        +'<option value="N">否</option>'
                    +'</select>'
                +'</td>'
                +'<td><input type="text" class="form-control" name="p[default][]" value="" placeholder="默认值"></td>'
                +'<td><input type="text" class="form-control" name="p[des][]" value="" placeholder="描述"></td>'
                +'<td><button type="button" class="btn btn-danger" onclick="del(this)">删除</button></td>'
            +'</tr>';
            $('#parameter1').append($html);
        }
        function del(obj){
            $(obj).parents('tr').remove();
        }
    </script>
    <!--添加接口 end-->
<?php }else if($op == 'edit'){ ?>
    <?php 
        $aid = I($_GET['tag']);    //所属分类
        $group_list=select("SELECT * FROM  `group` WHERE aid=".$aid);
    ?>
    <!--修改接口 start-->
    <div style="border:1px solid #ddd">
        <div style="background:#f5f5f5;padding:20px;position:relative">
            <h4>修改任务<span style="font-size:12px;padding-left:20px;color:#a94442">注:"此色"边框为必填项</span></h4>
            <div style="margin-left:20px;">
                <form action="?act=api&tag=<?php echo $_GET['tag']?>&type=do&op=edit" method="post">
                   <div>
                      <div class="col-md-3 form-group has-error" style="padding-left: 0px;">
                          <div class="input-group">
                              <div class="input-group-addon">接口名称</div>
                              <input type="text" class="form-control" name="name" placeholder="获取用户信息" value="<?php echo $info['name']?>" required="required">
                          </div>
                      </div>
                       <div class="col-md-3 form-group" required="required">
                          <div class="input-group">
                          <div class="input-group-addon">所属分组</div>
                              <select class="form-control" name="gid">
                                  <option value="0">其他分组</option>
                                  <?php
                                      foreach ($group_list as $key => $value) {
                                          $s='';
                                          if ($info['gid']==$value['id']) {
                                              $s='selected=""';
                                          }
                                          echo '<option value="'.$value['id'].'" '.$s.'>'.$value['name'].'</option>';
                                      }
                                  ?>
                              </select>
                          </div>
                      </div>
                      
                       <div class="col-md-3 form-group">
                          <div class="input-group">
                              <div class="input-group-addon">接口编号</div>
                              <input type="text" class="form-control" name="num" placeholder="接口编号" value="<?php echo $info['num']?>" required="required">
                          </div>
                      </div>
                       <div class="col-md-3 form-group">
                          <div class="input-group">
                              <div class="input-group-addon">排序</div>
                              <input type="text" class="form-control" name="ord" placeholder="排序" value="<?php echo $info['ord']?>">
                          </div>
                      </div>
                  </div>
                  <div>
                      <div class="col-md-4 form-group" style="padding-left: 0px;">
                          <div class="form-group has-error" >
                              <div class="input-group">
                                  <div class="input-group-addon">请求地址</div>
                                  <input type="text" class="form-control" name="url" placeholder="/user/getUserInfo" value="<?php echo $info['url']?>" required="required">
                              </div>
                          </div>
                          <div class="form-group" >
                          <div class="input-group">
                              <div class="input-group-addon">请求方式</div>
                              <select class="form-control" name="type">
                                  <option value="GET" <?php echo $info['type']=='GET'?'selected=""':''; ?>>GET</option>
                                  <option value="POST" <?php echo $info['type']=='POST'?'selected=""':''; ?>>POST</option>
                                  <option value="PUT" <?php echo $info['type']=='PUT'?'selected=""':''; ?>>PUT</option>
                                  <option value="DELETE" <?php echo $info['type']=='DELETE'?'selected=""':''; ?>>DELETE</option>
                              </select>
                          </div>
                      </div>
                      </div>
                      <div class="col-md-8 form-group">
                          <div class="input-group">
                              <div class="input-group-addon" style="height: 80px;">数据接口说明</div>
                              <textarea name="des" style="height: 80px;" class="form-control" placeholder="接口说明"><?php echo $info['des']?></textarea>
                          </div>
                      </div>
                  </div>
                  <div class="form-group">
                      <table class="table">
                          <thead>
                          <tr>
                              <th class="col-md-3">请求参数名</th>
                              <th class="col-md-2">必传</th>
                              <th class="col-md-2">缺省值</th>
                              <th class="col-md-4">描述</th>
                              <th class="col-md-1">
                                  <button type="button" class="btn btn-success" onclick="add1()">新增</button>
                              </th>
                          </tr>
                          </thead>
                          <style>
                              .u-d{
                                  float: left;
                                  width: 5.5%;
                              }
                              .u-d{
                                  cursor: pointer;
                              }
                              .u-d-i{
                                  width: 94.5%;
                                  float: right;
                              }
                          </style>
                          <tbody id="parameter1">
                              <tr></tr>
                              <?php
                                  foreach ($info['parameter']['name'] as $k_1 => $v_1) {
                              ?>
                                  <tr>
                                      <td class="form-group has-error">
                                          <div class="u-d"><span class="glyphicon glyphicon-arrow-up y-up"></span><span class="glyphicon glyphicon-arrow-down y-down"></span></div>
                                          <input type="text" class="form-control u-d-i" name="p[name][]" placeholder="参数名称" value="<?php echo htmlspecialchars($v_1); ?>" required="required">
                                      </td>
                                      <td class="form-group">
                                          <select class="form-control" name="p[type][]">
                                              <option value="Y" <?php echo $info['parameter']['type'][$k_1]=='Y'?'selected=""':'' ?>>是</option>
                                              <option value="N" <?php echo $info['parameter']['type'][$k_1]=='N'?'selected=""':'' ?>>否</option>
                                          </select>
                                      </td>
                                      <td>
                                          <input type="text" class="form-control" name="p[default][]" placeholder="默认值" value="<?php echo htmlspecialchars($info['parameter']['default'][$k_1]); ?>">
                                      </td>
                                      <td><input name="p[des][]" type="text" class="form-control" placeholder="描述" value="<?php echo ($info['parameter']['des'][$k_1]); ?>"/></td>
                                      <td><button type="button" class="btn btn-danger" onclick="del(this)">删除</button></td>
                                  </tr>
                              <?php
                                  }
                              ?>
                              <tr></tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="form-group">
                      <h5>返回结果</h5>
                      <textarea name="re" rows="5" class="form-control" placeholder="返回结果"><?php echo $info['re']?></textarea>
                  </div>
                  <div class="form-group">
                      <h5>备注</h5>
                      <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                      <textarea name="memo" rows="2" class="form-control" placeholder="备注"><?php echo $info['memo']?></textarea>
                  </div>
                  <button class="btn btn-success">Submit</button>
              </form>
            </div>
        </div>
    </div>
    <script>
        function add1(){
              var $html ='<tr>'
                +'<td class="form-group has-error">'
                    +'<div class="u-d"><span class="glyphicon glyphicon-arrow-up y-up"></span><span class="glyphicon glyphicon-arrow-down y-down"></span></div><input type="text" class="form-control u-d-i" name="p[name][]" placeholder="参数名称" value="">'
                +'</td>'
                +'<td>'
                    +'<select class="form-control" name="p[type][]">'
                        +'<option value="Y">是</option>'
                        +'<option value="N">否</option>'
                    +'</select>'
                +'</td>'
                +'<td><input type="text" class="form-control" name="p[default][]" value="" placeholder="默认值"></td>'
                +'<td><input type="text" class="form-control" name="p[des][]" value="" placeholder="描述"></td>'
                +'<td><button type="button" class="btn btn-danger" onclick="del(this)">删除</button></td>'
            +'</tr>';
            $('#parameter1').append($html);
        }
        function del(obj){
            $(obj).parents('tr').remove();
        }
        $(function(){
            $('#parameter1').on('click','.y-up',function(){
                var _this=$(this).parent().parent().parent();
                var up=_this.prev();
                console.log(up[0].tagName)
                if (up[0].tagName=='TR') {
                    exchange(_this,up)
                }
            })
            $('#parameter1').on('click','.y-down',function(){
                var _this=$(this).parent().parent().parent();
                var next=_this.next();
                console.log(next[0].tagName)
                if (next[0].tagName=='TR') {
                    exchange(next,_this)
                }
            })
            var exchange = function(a,b){
                var n = a.next(), p = b.prev();
                b.insertBefore(n);
                a.insertAfter(p);
            };
        })
    </script>
    <!--修改接口 end-->
<?php }else{ ?>
    <!--接口详细列表start-->
    <?php if(count($list)){ ?>
        <?php foreach($list as $v){ ?>
        <?php
        ?>
        <div class="info_api group-<?php echo $v['aid'].'-'.$v['gid']; ?>" style="border:1px solid #ddd;margin-bottom:20px;" id="info_api_<?php echo md5($v['id'])?>">
            <div style="background:#f5f5f5;padding:20px;position:relative">
                <div class="textshadow" style="position: absolute;right:0;top:4px;right:8px;">
                    最后修改者: <?php echo $v['nice_name']?> &nbsp;<?php echo date('Y-m-d H:i:s',$v['lasttime'])?>&nbsp;
                    <?php if(is_supper()){?>
                    <button class="btn btn-danger btn-xs " onclick="deleteApi(<?php echo $v['id']?>,'<?php echo md5($v['id'])?>')">delete</button>&nbsp;
                    <button class="btn btn-info btn-xs " onclick="editApi('<?php echo U(array('act'=>'api','op'=>'edit','id'=>$v['id'],'tag'=>$v['aid']))?>')">edit</button>
                    <?php } ?>
                </div>
                <h4 class="textshadow"><?php echo $v['name']?></h4>
                <p>
                    <b>编号&nbsp;&nbsp;:&nbsp;&nbsp;<span style="color:red"><?php echo $v['num']?></span></b>
                </p>
                <div>
                    <?php
                        $color = 'green';
                        if($v['type']=='POST'){
                            $color = 'red';
                        }
                    ?>
                    <kbd style="color:<?php echo $color?>"><?php echo $v['type']?></kbd> - <kbd><?php echo $v['url']?></kbd>
                </div>
            </div>
            <?php if(!empty($v['des'])){ ?>
            <pre class="info" style="white-space:pre-line">
                <?php echo $v['des']?>
            </pre>
            <?php } ?>
            <div style="background:#ffffff;padding:20px;">
                <h5 class="textshadow" >请求参数</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="col-md-3">参数名</th>
                        <th class="col-md-2">必传</th>
                        <th class="col-md-2">缺省值</th>
                        <th class="col-md-5">描述</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $parameter = unserialize($v['parameter']);
                        $pnum = count($parameter['name']);
                    ?>
                    <?php for( $i=0; $i<$pnum; $i++ ) {?>
                    <tr>
                        <td><?php echo $parameter['name'][$i]?></td>
                        <td><?php if($parameter['type'][$i]=='Y'){echo '<span style="color:red">是<span>';}else{echo '<span style="color:green">否<span>';}?></td>
                        <td><?php echo $parameter['default'][$i]?></td>
                        <td><?php echo $parameter['des'][$i]?></td>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
            <?php if(!empty($v['re'])){ ?>
            <div style="background:#ffffff;padding:20px;">
                <h5 class="textshadow" >返回值</h5>
                <pre class="i-json"><?php echo $v['re']?></pre>
            </div>
            <?php } ?>
            <?php if(!empty($v['memo'])){ ?>
            <div style="background:#ffffff;padding:20px;">
                <h5 class="textshadow">备注</h5>
                <pre style="background:honeydew"><?php echo $v['memo']?></pre>
            </div>
            <?php } ?>
        </div>
        <!--接口详细列表end-->
        <?php } ?>
    <?php } else{ ?>
        <div style="font-size:16px;">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 此分类下还没有任何接口
        </div>
    <?php }?>
    <script>
        //删除某个接口
        var $url = '<?php echo U(array('act'=>'ajax','op'=>'apiDelete'))?>';
        function deleteApi(apiId,divId){
            if(confirm('是否确认删除此接口?')){
                $.post($url,{id:apiId},function(data){
                    if(data == '1'){
                        $('#api_'+divId).remove();//删除左侧菜单
                        $('#info_api_'+divId).remove();//删除接口详情
                    }
                })
            }
        }
        //编辑某个接口
        function editApi(gourl){
            window.location.href=gourl;
        }
        $(function(){
            $('.i-json').each(function(){
                var text=$(this).text();
                try
                {
                    var result = JSON.stringify(JSON.parse(text), null, 4);//将字符串转换成json对象
                    $(this).html(result);
                }
                catch(err)
                {
                   //在此处理错误
                }
                
            })
        })
    </script>
<?php } ?>
<!--接口详情列表与接口管理end-->