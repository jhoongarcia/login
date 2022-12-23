<?php
session_start();
include "config.php";

$upload_error = $upload = $password_error = $message = "";

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

if (isset($_POST['change_photo'])) {

    $imgname = $_FILES['photo']['name'];
    $imgtype = $_FILES['photo']['type'];
    $imgtam = $_FILES['photo']['size'];

    // if (!is_dir('upload/'.$username)) {
    //     mkdir('upload/'.$username, 0777);
    // }

    if (($imgname == !NULL) && ($imgtam <= 2000000)) {
        if (($imgtype == "image/jpeg") || ($imgtype == "image/jpg") || ($imgtype == "image/png")) {
            $file = $username."-".$imgname;
            $file_loc = $_FILES['photo']['tmp_name'];
            $folder="upload/profile_pic/";
            $new_file_name = strtolower($file);
            $final_file=str_replace(' ','-',$new_file_name);
            if(move_uploaded_file($file_loc,$folder.$final_file)) {
                $sql = "UPDATE `users` SET `photo` = '$final_file' WHERE id  = '$id'";
                $sql = mysqli_query($conn,$sql) or die("Could Not Perform the Query");
                $upload = "your profile photo has changes sucess";
                header ("Location: profile.php?status=success");
            } else {
                $upload_error = "Error.Please try again";
            }
        } else {
            $upload_error = "No se puede subir una imagen con ese formato ";
        }
    } else {
        $upload_error = "La imagen es demasiado grande ";
    }
}
if (isset($_POST['change_password'])) {
    $npassword = $_POST['npassword'];
    $cnpassword = $_POST['cnpassword'];
    if(strlen($npassword) < 6){
        $password_error = "Password must have atleast 6 characters.";
    } 

    if(empty($password_error) && ($npassword != $cnpassword)){
        $password_error = "Password did not match.";
    }

    if (empty($password_error)) {
        $hash = password_hash($npassword, PASSWORD_BCRYPT);
        $sql = "UPDATE `users` SET `password` = '$hash' WHERE id  = '$id'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $message = "change password sucessful";
        } else {
            $password_error = "Oops! Something went wrong. Please try again later.";
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
        <nav>
            <a href="logout.php">Logout</a> 
            <a href="home.php">regresar</a> 
        </nav>
        <h1>BIENVENIDO A TU PERFIL   @<?php echo $user['username']; ?></h1>
        <div class="info_personal">
            <label for="name">Name: </label>
            <input type="text" name="name" id="name" placeholder="<?php echo $user['name']?>" disabled>
            <label for="email">Email: </label>
            <input type="text" name="email" id="email" placeholder="<?php echo $user['email']?>" disabled>
            <label for="username">Username: </label>
            <input type="text" name="username" id="username" placeholder="@<?php echo $user['username']?>" disabled>
            <label for="date">History register: </label>
            <input type="datetime" name="date" id="date" placeholder="<?php echo $user['datetime']?>" disabled>
        </div>
        <div class="edit_photo">
            <label for="photo">
                <img src="upload/profile_pic/<?php echo $user['photo'] ?>" height="150" width="150" style="border-radius:50%;" />
            </label>
            <label for="photo">upload photo</label>
            <?php if (!empty($upload) || !empty($upload_error)) { ?>
                <p> <?=$upload?> </p>
                <p> <?=$upload_error?> </p>
            <?php } ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="photo" id="photo" required>
                <button id="change_photo" name="change_photo">change photo</button>
            </form>
        </div>
        <div class="edit_pass">
            <form action="" method="post">
                <?php if (!empty($password_error) || !empty($message)) { ?>
                    <p> <?=$message?> </p>
                    <p> <?=$password_error?> </p>
                <?php } ?>
                <label for="npassword">Input new password</label>
                <input type="password" name="npassword" id="npassword" required>
                <label for="cnpassword">Confirm your new password</label>
                <input type="password" name="cnpassword" id="cnpassword" required>
                <button name="change_password">Change password</button>
            </form>
        </div>
    </div>
</body>

</html>