<?php

    @include 'db.php';
    
    session_start();

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception; 

    $con = mysqli_connect($servername, $username, $password, $database);

	function testinput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $nameErr = $phErr = $emailErr = $passErr= $passmatchErr = $isSent ="" ;
    $name_value = $phone_value = $email_value = $pass_value = $pass2_value="";
    // $_SESSION['login_attempts'] = 0 ;
    // $_SESSION['locked'] = '';


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
            $mail->Subject = 'Reset your password';
            $mail->Body    = 'http://localhost/UserFunction/internship/project-1/email_verification.php?email='.$email.'';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //get form data
    if(isset($_POST['submit'])){
        $name=$_POST['name'];
        $phone=$_POST['phone'];
        $email=$_POST['email'];
        $password=$_POST['pass'];
        $confirmpass=$_POST['pass2'];

        //name validation
        if (empty($name)) {
		    $nameErr = "Name is required";
            $phone_value=$phone;
            $email_value=$email;
            $pass_value=$password;
            $pass2_value=$confirmpass;
	    } 
        else {
            $username = testinput($name);
	        // check if name only contains letters and whitespace
	        if (!preg_match("/^[a-zA-Z ]*$/",$username)) {
	            $nameErr = "Only letters and white space allowed";
                $phone_value=$phone;
                $email_value=$email;
                $pass_value=$password;
                $pass2_value=$confirmpass;
	        }
        }
        
        //Phone number validation
        if(empty($phone)){
            $phErr="Phone number is required";
            $name_value=$name;
            $email_value=$email;
            $pass_value=$password;
            $pass2_value=$confirmpass;
        }else if(!preg_match("/^[1-9]{10}$/",$phone)) {
	            $phErr = "Invalid phone number";
                $name_value=$name;
                $email_value=$email;
                $pass_value=$password;
                $pass2_value=$confirmpass;
	        }

        // Validate email
        if(empty($email)){
            $emailErr="Email required";
            $name_value=$name;
            $phone_value=$phone;
            $pass_value=$password;
            $pass2_value=$confirmpass;
        }else {
            $email=testinput($email);
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $emailErr="Invalid email address";
                $name_value=$name;
                $phone_value=$phone;
                $pass_value=$password;
                $pass2_value=$confirmpass;
            }else{
        
                $sql= "SELECT * FROM `user` WHERE email = '$email'";
                $result=mysqli_query($con, $sql);

                if (mysqli_num_rows($result) > 0 ){
                    $emailErr = "This Email id already exists";
                    $name_value=$name;
                    $phone_value=$phone;
                    $pass_value=$password;
                    $pass2_value=$confirmpass;
                }
            }
        }

        //validate password
        if(empty($password)){
            $passErr="Password cannot be empty";
            $name_value=$name;
            $phone_value=$phone;
            $email_value=$email;
        }else {
            if (strlen($password)<8) {
                $passErr="Password must contain at least 8 character";
                $name_value=$name;
                $phone_value=$phone;
                $email_value=$email;
            }elseif(!preg_match("/[A-Z]+/", $password)){
                $passErr = "Password must contain at least one uppercase letter";
                $name_value=$name;
                $phone_value=$phone;
                $email_value=$email;
            }elseif(!preg_match("/[a-z]+/", $password)){
                $passErr = "Password must contain at least one lowercase letter";
                $name_value=$name;
                $phone_value=$phone;
                $email_value=$email;
            }elseif(!preg_match("/[^\w\s]+/", $password)){
                $passErr = "Password must contain at least one special character";
                $name_value=$name;
                $phone_value=$phone;
                $email_value=$email;
            }
        }
        if($password!==$confirmpass){
            $passmatchErr= "Password does not match";
            $name_value=$name;
            $phone_value=$phone;
            $email_value=$email;    
        }

        if (empty($nameErr) && empty($phErr) && empty($emailErr) && empty($passErr)){
            $_SESSION['email'] = $email;
            $_SESSION['status'] = false;
            $_SESSION['logged'] = true;
                
                    $sql = "INSERT INTO user (name,phone,email,password,role) VALUES ('$username','$phone','$email','$password','user')";
                    //Send mail to user
                    try{
                        $result = mysqli_query($con,$sql);
                        $isSent = sendMail($email);
                        if($isSent){
                            ?>
                                <script>
                                        alert('Email verification link hass been sent to your email address. Please verify.');
                                        window.location = 'login.php';
                                </script>
                            <?php
                        }else{
                            ?>
                            <script>
                                    alert('Invalid email address.');
                                    window.location = 'registration.php';
                            </script>
                        <?php
                        }
                    }catch(Exception $e){
                        echo "<script>alert('Error: please try after some time.')</script>";
                    }
                    
            }
        }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Registrtion</title>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
   
            <!-- font-family -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="assets/style.css">
        <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                transition: background-color 5000s ease-in-out 0s;
            }
        </style>
</head>
<body class="box">
    <section class="registration">
                <div class="container bg-white pb-2">  
                    <div class="row justify-content-center">

                    <div class="col-5 image">
                        <img class='img' src="assets/image1.png" alt="img">
                    </div>
                    <div class="col-5 data mt-2">
                        <div class="form">
                            <form id="form" class="" method="post" action="#">
                                <div class="form-heading">
                                    <h4>Create Account</h4>
                                </div>

                                <div class="form-floating">
                                    <input class="form-control shadow-none" type="text" name="name" id="name" value="<?php echo $name_value; ?>" placeholder="Name">
                                    <label for="name">Name </label>
                                        <span class="error text-danger"><?php echo $nameErr; ?></span>
                                </div>

                                <div class="form-floating">
                                    <input class="form-control shadow-none" type="tel" name="phone" id="phone" value="<?php echo $phone_value; ?>" placeholder="Phone">
                                    <label class="form-label" for="phone">Phone</label>
                                        <span class="error text-danger"><?php echo $phErr; ?></span>
                                </div>
                                <div class="form-floating">
                                    <input class="form-control shadow-none" type="email" name="email" id="email" value="<?php echo $email_value; ?>" placeholder="Email">
                                    <label class="form-label" for="email">Email </label>
                                        <span class="error text-danger"><?php echo $emailErr; ?></span>
                                </div>
                                <div class="form-floating">
                                    <input class="form-control shadow-none" type="password" name="pass" id="password" value="<?php echo $pass_value; ?>" placeholder="Password">
                                    <label class="form-label" for="password">Password</label>
                                        <span class="error text-danger"><?php echo $passErr; ?></span>
                                </div>
                                <div class="form-floating ">
                                    <input class="form-control shadow-none" type="password" name="pass2" id="confirm_password" value="<?php echo $pass2_value; ?>" placeholder="Confirm Password" >
                                    <label class="form-label" for="password2">Confirm Password </label>
                                    <div class="error">
                                        <span class="text-danger"><?php echo $passmatchErr; ?></span>
                                    </div>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" onclick="myFunction();">&nbsp;&nbsp;Show Password
                                </div>
                                <div class="submit">
                                    <input type="submit" class="btn" name="submit" value="Register">
                                </div>
                                <p class="">already have an account? 
                                    <a href="login.php">Login here</a>
                                </p>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>

    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
            }
    </script>
    <script>
        function myFunction() {
            var password = document.getElementById("password");
            var confirm_password = document.getElementById("confirm_password");

            if(password.type === 'password'){
                password.type = 'text'; 
                confirm_password.type= 'text';
            }else{
                password.type = 'password'; 
                confirm_password.type = 'password';
            }
        }
    </script>
</body>
</html>