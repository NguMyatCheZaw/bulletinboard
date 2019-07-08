<!doctype html>
<html>
    <head>
        <title>Registration</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>

        <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
            <div class="card">
            <div class="card-body">
  
            <!-- Nav bar -->
            <nav class="navbar navbar-default rounded">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <h2 class="navbar-brand">SCM Bulletin Board</h2>
                    </div>
                    <ul class="nav nav-pills">
                        <li class="active pl-3"><a href="#">ユーザ</a></li>
                        <li class="pl-3"><a href="#">投稿</a></li>
                    </ul>
                    <ul class="nav nav-pills navbar-right">
                        <li class="pl-3"><a href="#"><span class=""></span>プロフィール</a></li>
                        <li class="pl-3"><a href="#"><span class=""></span>ログアウト</a></li>
                    </ul>
                </div>
            </nav>

            <!-- another nav form -->
            <!-- <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>                        
                </button>
                <a class="navbar-brand" href="#">SCM Bulletin Board</a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">ユーザ</a></li>
                        <li><a href="#">投稿</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><span class="glyphicon glyphicon-user"></span>プロフィール</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-log-in"></span>ログアウト</a></li>
                    </ul>
                </div>
            </div>
            </nav> -->

            <!-- Registration Form -->
            <div class="title mt-5 mb-3">
                Create User
            </div>

            <div class="row">
            <div class="col-lg-8 mx-auto">
            <form method="POST" action="/register" class="form-horizontal">
            
                <div class="form-group">
                    <label for="name" class="control-label col-sm-4">Name</label>
                    <input type="text" id="name" name="name" class="">
                </div>
                <div class="form-group">
                    <label for="email" class="control-label col-sm-4">Email Address</label>
                    <input type="text" id="email" name="email" class="">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label col-sm-4">Password</label>
                    <input type="password" id="password" name="password" class="">
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="control-label col-sm-4">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="">
                </div>
                <div class="form-group">
                    <label for="type" class="control-label col-sm-4">Type</label>
                    <select id="type" name="type" class="">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone" class="control-label col-sm-4">Phone</label>
                    <input type="text" id="phone" name="phone" class="">
                </div>
                <div class="form-group">
                    <label for="dob" class="control-label col-sm-4">Date Of Birth</label>
                    <div class="d-inline" id="datetimepicker1">
                        <input type="text" id="dob" name="dob" class="" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <script type="text/javascript">
                    
                        $('#datetimepicker1').datetimepicker();
                    
                </script>
                <div class="form-group">
                    <label for="address" class="control-label col-sm-4">Address</label>
                    <textarea id="address" name="address" class="" rows=4></textarea>
                </div>
                <div class="form-group">
                    <label for="profile" class="control-label col-sm-4">Profile</label>
                    <input type="file" id="profile" name="profile" class="">
                </div>

                <div class="row pt-3">
                    <div class="row mx-auto col-sm-4 col-md-6">
                        <button type="submit" class="btn btn-primary mr-5">Confirm</button>
                        <button type="button" class="btn btn-default">Clear</button>
                    </div>
                </div>

            </form>
            </div>
            </div>

            </div>
            </div>
            </div>
        </div>
        </div>
    
    </body>
</html>
