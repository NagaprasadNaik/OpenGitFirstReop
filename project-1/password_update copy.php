<?php

    @include 'db.php';

    session_start();
    
    if(!$_SESSION['logged']){
        header('location : login.php');
    }

    $email = $_SESSION['email'];
    $status = 0;
    $confirmPassword = $newPassword = $error = $passError = '';
    // Create connection
    $con = mysqli_connect($servername, $username, $password, $database);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;  


    function sendMail($otp, $email){
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
            $mail->Subject = 'Email verification with One Time Password';
            $mail->Body    = 'One Time Password : '. $otp .'. This one time password will only valid for 60 seconds!';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    if(isset($_POST['get-otp'])){
        $otp = random_int(111111, 999999);
        $flag = sendMail($otp, $email);
        if($flag){
            $sql = "UPDATE `user` SET `otp`= $otp, `date_time` = now() WHERE email = '$email' ";
            $result =  mysqli_query($con, $sql);
            $status = 1;
            echo '<script>alert("OTP has been sent to your email address!. Please check your Email!")</script>';
        }else{
            echo '<script>alert("Invalid Email adress!")</script>';
            $status = 0;
        }
    }

    if(isset($_POST['enter-otp'])){
        $otp2 = $_POST['otp'];
        if($otp2 != ''){
            $sql2 = "SELECT * FROM `user` WHERE otp = $otp2 AND now() <= date_add(date_time, INTERVAL 1 MINUTE) ";
            $result2 =  mysqli_query($con, $sql2);
            $row = mysqli_fetch_assoc($result2);
            if($row){
                $status = 2;
            }else{
                echo "<script>alert('Invalid OTP!')</script>";
                $status = 0;
            }
        }else{
            $error = 'Please enter OTP';
            $status = 1;
        }
    }

    if(isset($_POST['save'])){
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if(empty($newPassword) && empty($confirmPassword)){
            $passError="Password cannot be empty!";
            $error="Password cannot be empty!";
            $status = 2;
        }
        else if(empty($newPassword)){
            $passError="Password cannot be empty!";
            $status = 2;
        }else if(empty($confirmPassword)){
            $error="Password cannot be empty!";
            $status = 2;
        }else {
            if (strlen($newPassword)<8) {
                $passError="Password must contain at least 8 character";
                $status = 2;
            }else if(!preg_match("/[A-Z]+/", $newPassword)){
                $passError = "Password must contain at least one uppercase letter!";
                $status = 2;
            }else if(!preg_match("/[a-z]+/", $newPassword)){
                $passError = "Password must contain at least one lowercase letter!";
                $status = 2;
            }else if(!preg_match("/[^\w\s]+/", $newPassword)){
                $passError = "Password must contain at least one special character!";
                $status = 2;
            }else if($newPassword!=$confirmPassword){
                $error= "Confirm Password does not match!";   
                $status = 2;
            }else{
                $sql3 = "UPDATE `user` SET password = '$newPassword' WHERE email = '$email' ";
                $result3 = mysqli_query($con, $sql3);
                if($result3){
                    $_SESSION['status'] = true;
                    header('location: profile.php');
                }else{
                    $_SESSION['status'] = false;
                    header('loaction: password_update.php');
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
    <title>Password Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <style>
        #error{ color:red;}
        .navbar-dark .navbar-nav  .nav-link:hover{color: orange;}
    </style>
</head>
<body class="bg-light">
    <!-- navbar start -->
    <nav class="navbar navbar-expand navbar-dark bg-dark pt-4 pb-4">
    <div class="container">
    <a href="profile.php" class="navbar-brand fw-bold fs-4 border border-1 rounded p-1">Profile</a>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link active ms-2 me-2 fw-bold " href="profile.php" role="button">
            Profile
          </a>
        </li>
        <li class="nav-item">
            <a href="mail.php" class="nav-link active ms-2 me-2 fw-bold" >Mail</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link active ms-2 me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal">Logout</a>
            <div class="modal fade" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                           <p class="fw-bold">Confirm Logout</p> 
                        </div>
                        <div class="modal-body mt-3 mb-3 ps-5 ">
                           <p>Are you sure! you want to logout?</p> 
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#modal">No</button> 
                           <a href="logout.php"><button class="btn btn-success">Yes</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    </div>
    </nav>
    <!-- navbar End -->

    <div class="container ">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6 mt-5 pt-5 border border-2 rounded-3 bg-white">
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email : </label>
                        <input type="text" class="form-control" name="email" value="<?php echo $email?>" readonly>
                    </div>

                    <!-- status 0 -->
                    <?php
                        if ($status == 0){
                    ?>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary mb-2 " name="get-otp">Get OTP</button>               
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
                    

                    <!-- status 2 -->
                    <?php
                        if ($status == 2){
                    ?>
                    <div class="mb-3">
                        <label for="Enter New Password" class="form-label">Enter New Password : </label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" value="<?php echo $newPassword?>">
                        <span id="error"><?php echo $passError?></span>
                    </div>
                    <div class="mb-3">
                        <label for="Confirm Password" class="form-label">Confirm Password : </label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $confirmPassword?>">
                        <span id="error"><?php echo $error?></span>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="check" class="form-check-input" onclick="showPassword()">
                        <label for="check" class="form-check-label">Show Password</label>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary mb-2 " name="save">Save</button>               
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


    //Display password 
    function showPassword(){
        var newPassword = document.getElementById("newPassword");
        var confirmPassword = document.getElementById("confirmPassword");
        if (newPassword.type == 'password'){
            newPassword.type = 'text';
            confirmPassword.type = 'text';
        }else{
            newPassword.type = 'password';
            confirmPassword.type = 'password';
        }
    }

    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
</body>
</html>
