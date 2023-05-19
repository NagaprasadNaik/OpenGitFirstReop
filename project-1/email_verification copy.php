<?php

    @include 'db.php';
    session_start();
    $con = mysqli_connect($servername, $username, $password, $database);
    $email = $_SESSION['email'];
    // $status = 1;
    // $error = $otp = '';
    // $attempts =  $_SESSION['attempts'] + 1;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;


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
            $mail->Body    = '';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    if(isset($_POST['enter-otp'])){
        $otp2 = $_POST['otp'];
        // $attempts += 1;

        $sql = "UPDATE user SET otp_attempts = '$attempts'";
        $result = mysqli_query($con, $sql);

        $sql2 = "SELECT otp_attempts FROM user WHERE email ='$email' ";
        $otp_attempts = mysqli_query($con, $sql2);
        $result2 = mysqli_fetch_assoc($otp_attempts);

        if($otp2 != ''){
            $sql3 = "SELECT * FROM `user` WHERE otp = $otp2 and email = '$email' ";
            $result3 =  mysqli_query($con, $sql3);
            $row = mysqli_fetch_assoc($result3);
            if($row){
                $sql4 = "UPDATE user SET otp_status = 1 , mail_status = 1, otp_attempts = 0 WHERE email = '$email' ";
                $result4 =  mysqli_query($con, $sql4);
                $_SESSION['attempts'] = 0;
                header('location:profile.php');
            }else{
                $error = "Invalid OTP";
                $_SESSION['attempts'] += 1;
            }
        }else{
            $error = 'Please enter OTP';
            $_SESSION['attempts'] += 1;
        }

        if($result2['otp_attempts'] >= 3){
            $status = 0;
            echo '<script>alert("You have exceeded maximum limit!")</script>';
        }
    }

    if(isset($_POST['get-otp'])){
        // $email = $_POST['email'];
        $otp = random_int(111111, 999999);
        $flag = sendMail($otp, $email);
        if($flag){
            $sql = "UPDATE `user` SET `otp`= $otp, `date_time` = now() WHERE email = '$email' ";
            $result =  mysqli_query($con, $sql);
            $status = 1;
            echo '<script>alert("OTP has been sent to your email address!. Please check your Email!")</script>';
        }else{
            $error = 'Invalid Email!';
            $status = 0;
        }
    }
?>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <style>
        #error{ color:red;}
    </style>
</head>
<body>
<div class="container ">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6 mt-5 pt-5 border border-2 rounded-3 bg-white">
                <form action="#" method="POST">
                    <!-- status 0 -->
                    <?php
                        if ($status == 0){
                    ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email : </label>
                        <input type="text" class="form-control" name="email" value="<?php echo $email ?>" readonly>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary mb-2 " name="get-otp">Resend OTP</button>               
                        </div>                   
                    </div>
                    <?php
                        }
                    ?>

                    <!-- status 1 -->
                    <?php
                        if ($status == 1){
                    ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email : </label>
                        <input type="text" class="form-control" name="email" value="<?php echo $email ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">OTP : </label>
                        <input type="text" class="form-control" name="otp" placeholder="Enter OTP">
                        <span id="error" ><?php echo $error?></span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary mb-2 " name="enter-otp">Enter OTP</button>               
                        </div>                   
                    </div>
                    <?php
                        }
                    ?>
                    
                </from>
            </div>
        </div>
    </div>

    <!-- prevents form resubmission -->
    <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
</body>
</html> -->