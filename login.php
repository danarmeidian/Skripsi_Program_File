<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="assets/favicon.ico" />
    <title>Login</title>
    <link href="assets/css/styles.css?v=1.4" rel="stylesheet" />
    <link href="assets/fontawesome/css/all.min.css" rel="stylesheet" />
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-4"><br><br><br>
        <div class="row">
            <div class="col-md-4 mx-auto">
                <form method="post" action="?m=login">
                    <div class="card">
                        <div class="card-header-login">
                            Login
                        </div>
                        <div class="card-body-login">
                            <?php if ($_POST) include 'aksi.php'; ?>
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" placeholder="Username" name="user" autofocus />
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="pass" />
                            </div>
                        </div>
                        <div class="card-footer-login">
                            <button class="btn btn-primary" type="submit"><span class="fa fa-right-to-bracket"></span> Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>