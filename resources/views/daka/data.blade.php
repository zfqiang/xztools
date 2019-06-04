<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bootstrap 实例 - 标签页（Tab）插件方法</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<h1 class="text-center">
    打卡考勤数据
</h1>
<br/><br/>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <div class="tab-pane active" id="panel-758874">
                <form  action="/daka/dakaData" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    姓名：<input value="{{$name}}" name="name">
                    <button type="submit" class="btn btn-success">提交</button>
                    <button type="button" class="btn btn-info" onclick="window.location.href = '/daka/index'">返回</button>
                </form>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>
                        编号
                    </th>
                    <th>
                        姓名
                    </th>
                    <th>
                        打卡时间
                    </th>
                    <th>
                        部门
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($datas as $key => $data)
                    <tr @if (($key + 1) % 2 == 0)class="success" @endif>
                        <td>
                            {{$key}}
                        </td>
                        <td>
                            {{$data->name}}
                        </td>
                        <td>
                            {{$data->date_time}}
                        </td>
                        <td>
                            {{$data->department}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $datas->links() !!}
        </div>
    </div>
</div>
</body>
</html>
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