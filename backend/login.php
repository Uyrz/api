<?php
session_start();
require '../vendor/autoload.php';

use Firebase\JWT\JWT;

$error = '';

if (isset($_POST["login"])) {
    try {
        $connect = new PDO("mysql:host=localhost;dbname=dataapp", "root", "");

        if (empty($_POST["username"])) {
            $error = 'Please enter your username.';
        } else if (empty($_POST["password"])) {
            $error = 'Please enter your password.';
        } else {
            $query = "SELECT * FROM users WHERE username = ?";
            $statement = $connect->prepare($query);
            $statement->execute([$_POST["username"]]);

            $data = $statement->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                if (password_verify($_POST['password'], $data['password'])) {
                    $key = '1a3LM3W966D6QTJ5BJb9opunkUcw_d09NCOIJb9QZTsrneqOICoMoeYUDcd_NfaQyR787PAH98Vhue5g938jdkiyIZyJICytKlbjNBtebaHljIR6-zf3A2h3uy6pCtUFl1UhXWnV6madujY4_3SyUViRwBUOP-UudUL4wnJnKYUGDKsiZePPzBGrF4_gxJMRwF9lIWyUCHSh-PRGfvT7s1mu4-5ByYlFvGDQraP4ZiG5bC1TAKO_CnPyd1hrpdzBzNW4SfjqGKmz7IvLAHmRD-2AMQHpTU-hN2vwoA-iQxwQhfnqjM0nnwtZ0urE6HjKl6GWQW-KLnhtfw5n_84IRQ';
                    $token = JWT::encode(
                        array(
                            'iat' => time(),
                            'nbf' => time(),
                            'exp' => time() + 3600,
                            'data' => array(
                                'id' => $data['id'],
                                'username' => $data['username']
                            )
                        ),
                        $key,
                        'HS256'
                    );
                    setcookie("token", $token, time() + 3600, "/", "", true, true);
                    $_SESSION['user_id'] = $data['id'];
                    $_SESSION['user_rank'] = $data['rank'];
                    header('location:index.php');
                    exit();
                } else {
                    $error = 'Invalid Password';
                }
            } else {
                $error = 'Invalid Username';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <?php
            if ($error !== '') {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }
            ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Login</h3>
                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" autocomplete="username" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" autocomplete="current-password" class="form-control" required />
                        </div>
                        <div class="d-grid">
                            <input type="submit" name="login" class="btn btn-primary" value="Login" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
