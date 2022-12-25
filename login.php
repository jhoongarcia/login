<?php
session_start();
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
}
include "config.php";

$message = $username_error = $signUp_error = $signIn_error = $password_error = "";

if (isset($_POST['signUp'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if(!preg_match('/^[a-zA-Z0-9_.-ñ]+$/', $username)){
        $username_error = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "Select * from users where username='$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_num_rows($result);
        if ($row != 0) {
            $username_error = "Username not available";
        }
    }
    $sql = "Select * from users where email='$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);
    if ($row != 0) {
        $signUp_error = "This email is already registered";
    }

    if(strlen($password) < 6){
        $password_error = "Password must have atleast 6 characters.";
    } 

    if(empty($password_error) && ($password != $cpassword)){
        $password_error = "Password did not match.";
    }

    if (empty($signUp_error) && empty($username_error) && empty($password_error)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO `users`(`id`, `name`, `email`, `username`, `password`, `datetime`) VALUES (NULL, '$name', '$email', '$username', '$hash', CURRENT_TIMESTAMP())";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $message = "sucess";
        } else {
            $signUp_error = "Oops! Something went wrong. Please try again later.";
        }
    }
}

if (isset($_POST['signIn'])) { 
    $email = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = '$email' OR username = '$email'";
    $sql = mysqli_query($conn, $sql);
    $row  = mysqli_fetch_array($sql);

    if (!$row) {
        $signIn_error = "Invalid username or password.";
    } else {
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username']; 
            header("Location:home.php"); 
        } else {
            $signIn_error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <!--SignUp-->
        <div class="signUp" >
            <form action="" method="post" name="register">
                <h1>Crea tu cuenta</h1>
                <?php if (!empty($message) || !empty($signUp_error) || !empty($username_error) || !empty($password_error)) { ?>
                    <p><?= $message ?></p>
                    <p><?= $signUp_error ?></p>
                    <p><?= $username_error ?></p>
                    <p><?= $password_error ?></p>
                <?php } ?>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Input your name" required>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Input your email" required>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Input your @username" required>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Input your password" required>
                <label for="password">Confirm password</label>
                <input type="password" name="cpassword" id="cpassword" placeholder="confirm your password" required>
                <button id="signUp" name="signUp">Register</button>
            </form>
        </div>

        <!--SigIn-->
        <div class="signIn">
            <form action="" method="post">
                <h1>Iniciar Sesión</h1>
                <?php if (!empty($signIn_error)) { ?>
                    <p><?= $signIn_error ?></p>
                <?php } ?>
                <label for="text_login">Email or username</label>
                <input type="text" name="username" id="text_login" placeholder="Input your email or user" required>
                <label for="password_login">Password</label>
                <input type="password" name="password" id="password_login" placeholder="Input your password" required>
                <button id="signIn" name="signIn">Login</button>
            </form>
        </div>
        <div class="back">
            <a href="index.html">regresar</a>
        </div>
    </div>
</body>
</html>
