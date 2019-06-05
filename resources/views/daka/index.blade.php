<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>打卡考勤数据处理</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<h1 class="text-center">
    打卡考勤数据处理
</h1>
<br/><br/>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <div class="tabbable" id="tabs-804584">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#panel-758874" data-toggle="tab">导入打卡考勤数据</a>
                    </li>
                    <li>
                        <a href="#panel-149161" data-toggle="tab">导出打卡考勤数据</a>
                    </li>
                    <li>
                        <a href="#panel-149162" data-toggle="tab">导入人员部门数据</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="panel-758874">
                        <br/><br/>
                        <form  action="/daka/importData" id="dakaForm" method="post" enctype="multipart/form-data" onsubmit="subdakaForm();return false;">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label for="exampleInputFile">选择要导入的考勤数据</label><input type="file" id="file" name="file"/>
                            </div>
                            <button type="submit" class="btn btn-success">提交</button>
                        </form>
                        <br/>
                        <a type="button" class="btn btn-info" target="_blank" href="/files/template.xls">下载导入文件模板</a>
                        <a type="button" class="btn btn-info" target="_blank" href="/daka/dakaData">查看详细的打卡记录</a>
                    </div>
                    <div class="tab-pane" id="panel-149161">
                        <br/><br/>
                        <button type="button" class="btn btn-success" onclick="window.location.href='/daka/exportData'">导出打卡考勤数据</button>
                    </div>
                    <div class="tab-pane" id="panel-149162">
                        <br/><br/>
                        <form action="/daka/importMembers" id="memberForm" method="post" enctype="multipart/form-data" onsubmit="submemberForm();return false;">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label for="exampleInputFile">选择文件</label><input type="file" id="mfile" name="mfile" />
                            </div>
                            <button type="submit" class="btn btn-success">提交</button>
                        </form>
                        <br/>
                        <a type="button" class="btn btn-info" target="_blank" href="/daka/memberData">查看详细的人员部门数据</a>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">提示信息</h4>
            </div>
            <div class="modal-body">按下 ESC 按钮退出。</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->
</body>
</html>
<script>
    @if($type == 'importMembers')
        @if(empty($page))
        $('#myModal .modal-body').html('数据导入成功');
        $('#myModal').modal('show');
        @endif
        $('#tabs-804584 > ul > li').eq(0).removeClass('active');
        $('#panel-758874').removeClass('active');

        $('#tabs-804584 > ul > li').eq(1).removeClass('active');
        $('#panel-149161').removeClass('active');

        $('#tabs-804584 > ul > li').eq(2).addClass('active');
        $('#panel-149162').addClass('active');
    @endif

    @if($type == 'importData')
        $('#myModal .modal-body').html('数据导入成功');
        $('#myModal').modal('show');

        $('#tabs-804584 > ul > li').eq(0).addClass('active');
        $('#panel-758874').addClass('active');

        $('#tabs-804584 > ul > li').eq(1).removeClass('active');
        $('#panel-149161').removeClass('active');

        $('#tabs-804584 > ul > li').eq(2).removeClass('active');
        $('#panel-149162').removeClass('active');
    @endif
    function subdakaForm() {
        if($('#file').val() == ''){
            $('#myModal .modal-body').html('上传的附件不能为空');
            $('#myModal').modal('show');
            return false;
        }
        $("#dakaForm").submit();
    }
    function submemberForm() {
        if($('#mfile').val() == ''){
            $('#myModal .modal-body').html('上传的附件不能为空');
            $('#myModal').modal('show');
            return false;
        }
        $("#memberForm").submit();
    }

</script>
<style>
    html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        font-weight: 200;
        height: 100vh;
        margin: 0;
    }

    .full-height {
        height: 100vh;
    }

    .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }

    .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
    }

    .content {
        text-align: center;
    }

    .title {
        font-size: 84px;
    }

    .links > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    .m-b-md {
        margin-bottom: 30px;
    }

    #myTab > li > a {
        font-size: 30px;
    }
</style>