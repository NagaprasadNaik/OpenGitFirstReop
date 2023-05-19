<?php

@include 'db.php'; 

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//For sweet alert and alert message
$_SESSION['status'] = false;
$_SESSION['flag'] = false;
$error = $limit = $remaining_attempts = $timeout = '';

$con = mysqli_connect($servername, $username, $password, $database);


function sendMail($email){
    require 'PhpMailer/PHPMailer.php';
    require 'PhpMailer/SMTP.php';
    require 'PhpMailer/Exception.php';

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'openmail12345678@gmail.com';                     
        $mail->Password   = 'dscqqpxoeschccez';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->Port       = 465;                                    
    
        //Recipients
        $mail->setFrom('openmail12345678@gmail.com', 'Prasad');   
        $mail->addAddress($email);               

        //Content 
        $mail->isHTML(true);                                  
        $mail->Subject = 'Email verification.';
        $mail->Body    = 'http://localhost/UserFunction/internship/project-1/email_verification.php?email='.$email.'';
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}


if(isset($_POST['submit'])){
   
   $email = $_POST['email'];
   $pass = $_POST['password'];

   $ip = $_SERVER['REMOTE_ADDR'];   
   $login_time = time() - 15;
   $sql = "SELECT count(*) as total_count FROM login_limit WHERE ip='$ip' AND login_time>'$login_time'";
   $result = mysqli_query($con, $sql);
   $row = mysqli_fetch_assoc($result);
   $count = $row['total_count'];
   $count++;
   $remaining_attempts = 3 - $count;

   if($remaining_attempts == 0){
      $timeout = "Please try after 15 seconds";
   }else{

      if($email == '' || $pass == ''){
         $error = 'Incorrect email or password!';
         $limit = 'Remainig attempts '.$remaining_attempts;
         $ip = $_SERVER['REMOTE_ADDR'];
         $login_time = time();
         $sql = "INSERT INTO login_limit(ip, login_time) VALUES('$ip', '$login_time')";
         $result = mysqli_query($con, $sql);
      }else{

         $con = mysqli_connect($servername, $username, $password, $database);
         
         $select = "SELECT * FROM user WHERE email = '$email' && password = '$pass' ";

         $result = mysqli_query($con, $select);


         if(mysqli_num_rows($result) > 0){

            $row = mysqli_fetch_assoc($result);

            if($row['role'] == 'admin'){
               $_SESSION['logged'] = true;
               $_SESSION['email'] = $row['email'];
               $_SESSION['role'] = $row['role'];
               $sql = "DELETE FROM login_limit WHERE ip='$ip'";
               $result = mysqli_query($con, $sql);
               header('location:admin.php');
            }elseif($row['role'] == 'user'){
               if($row['mail_status'] == 1){
                  $_SESSION['logged'] = true;
                  $_SESSION['email'] = $row['email'];
                  $sql = "DELETE FROM login_limit WHERE ip='$ip'";
                  $result = mysqli_query($con, $sql);
                  header('location:profile.php');
               }else{
                  $_SESSION['email'] = $email;
                  $flag = sendMail($email);
                  if($flag){
                     echo "<script>alert('Please verify your email address. Verification link has been sent to your email!')</script>";
                  }else{
                     echo "<script>alert('Invaild email address!')</script>";
                  }
               }
            }
         }else{
            $error = 'Incorrect email or password!.';
            $limit = 'Remainig attempts '.$remaining_attempts;
            $ip = $_SERVER['REMOTE_ADDR'];
            $login_time = time();
            $sql = "INSERT INTO login_limit(ip, login_time) VALUES('$ip', '$login_time')";
            $result = mysqli_query($con, $sql);
         }
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
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
   <title>login form</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
   <link rel="stylesheet" href="assets/style.css">
    <!-- font-family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">

    <style>
      input:-webkit-autofill,
      input:-webkit-autofill:hover,
      input:-webkit-autofill:focus,
      input:-webkit-autofill:active {
         -webkit-text-fill-color: black;
         transition: background-color 5000s ease-in-out 0s;
      }
   </style>
</head>
<body class="login-area">  
<section >
   <div class="container">
      <div class="row login-row">
         <div class="col-sm-6 login-img">
            <img class="img" src="assets/images/log.png" alt="">
         </div>
         <div class="col-sm-6 login-fields bg-white">
            <div class="form-container">

               <form action="" method="post">
                  <div class="error">
                     <span id="timeout"><?php echo $timeout ?></span>
                     <span id="error"><?php echo $error ?></span><br>
                     <span id="error"><?php echo $limit ?></span>
                  </div>

                  <div class="heading">
                     <h3>login</h3>
                  </div>
                     
                  <div class="form-floating">
                     <i class="fa-regular fa-envelope"></i>
                     <input class="form-control shadow-none" type="text" name="email" id="email" value="" placeholder="Email">
                     <label for="email">Email </label>
                  </div>

                  <div class="form-floating">
                     <span class="eye" id="hide" onclick="toggle()">
                        <i class="fa-sharp fa-regular fa-eye" id="eye"></i>
                        <i class="fa-sharp fa-regular fa-eye-slash" id="eyeSlash"></i>
                     </span>
                     <input class="form-control shadow-none" type="password" name="password" id="password" value="" placeholder="Password">
                     <label for="password">Password </label>
                  </div>

                  <div class="forgot-pass">
                     <p><a href="forgot_pwd.php?id=0">forgot password?</a></p>
                  </div> 

                  <div class="sbt-btn"> 
                     <input type="submit" id="submit" name="submit" value="login" class="form-btn">
                     <p>Don't have an account? <a href="registration.php">Register now</a></p>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

   <?php 
      if($remaining_attempts == 0){
         ?>
            <script>
               const sbmt = document.getElementById('submit');
               const timeout = document.getElementById('timeout');
               var seconds = 14;
               sbmt.setAttribute("disabled", "disabled");
               sbmt.style.background = '#b8b9b9';
               sbmt.style.color = '#403e3e';

               setInterval(function(){
                  timeout.innerHTML = "Please try after "+seconds+" seconds";
                  seconds -= 1;
               }, 1000);

               setTimeout(function(){
                  window.location.reload(1);
               }, 15000);
            </script>
         <?php
      }else{
         ?>
            <script>
               const sbmt = document.getElementById('submit');
               sbmt.removeAttribute("disabled");
            </script>
         <?php
      }
   ?>
   
   <script>

      if ( window.history.replaceState ) {
         window.history.replaceState( null, null, window.location.href );
      }

      const toggle = () => {
         const x = document.getElementById('password');
         const y = document.getElementById('eye');
         const z = document.getElementById('eyeSlash');

         if(x.type == 'password'){
            x.type = "text";
            y.style.display = "block";
            z.style.display = "none";
         }else{
            x.type = "password";
            y.style.display = "none";
            z.style.display = "block";
         }
      }
   </script>
   <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/9d09e1757b.js" crossorigin="anonymous"></script>
</body>
</html>
