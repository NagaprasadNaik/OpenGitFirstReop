<?php 
    @include 'db.php';

    session_start();
    $email = $_SESSION['email'];
    $con = mysqli_connect($servername, $username, $password, $database);
    $status = "UPDATE user SET otp_attempts = 0 WHERE email = '$email'";
    $result2 = mysqli_query($con, $status);
    $_SESSION['logged'] = false;
    // $_SESSION['login_attempts'] = 0;


    session_unset();
    session_destroy();

    header('location: login.php');
    exit;
?>