<?php
session_start();
include "config.php";

$upload_error = "";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
} else {
    $id = $_SESSION["id"];
    $username = $_SESSION["username"];
    $sql = "SELECT * FROM users where id = '$id'";
    $sql = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($sql);
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
        <nav>
            <a href="logout.php">Logout</a>
            <a href="profile.php">editar perfil</a>
        </nav>
        <h1>FELICIDADES ACABAS DE INGRESAR </h1>
        <p>    @<?php echo $user['username']; ?></p>
        <img src="upload/profile_pic/<?php echo $user['photo'] ?>" height="150" width="150" style="border-radius:50%;" />
        <p> <?=$upload_error?> </p>

        <section>
            <h2>usuarios</h2>
            <?php
            $sql = "SELECT * FROM users ORDER BY id ASC;";
            $sql = mysqli_query($conn, $sql);
            $users = mysqli_fetch_array($sql);
            ?>
            <p>@<?php echo $users['username']; ?></p>
            <img src="upload/profile_pic/<?php echo $users['photo'] ?>" height="150" width="150" style="border-radius:50%;" />
        </section>
    </div>
</body>

</html>