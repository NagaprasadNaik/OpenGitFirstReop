<?php

    @include 'db.php';
    
    session_start();

    $con = mysqli_connect($servername, $username, $password, $database);

	function testinput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $nameErr = $phErr = $emailErr = $passErr= $passmatchErr ="" ;
    $name_value = $phone_value = $email_value = $pass_value = $pass2_value="";

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
                //insert data into DB
                // $hashed_pass = password_hash($password,PASSWORD_DEFAULT);
                // $sql = "INSERT INTO user (name,phone,email,password,role) VALUES ('$username','$phone','$email','$password','user')";
                
                    $otp = rand(100000,999999);
                    $sql = "INSERT INTO user (name,phone,email,password,role,otp) VALUES ('$username','$phone','$email','$password','user',$otp)";
                    //Send mail to user
                    $to = $email;
                    $subject = 'Email verification code';
                    $message = 'your 6 digit registration otp is '. $otp . '.';
                    $headers = 'From: webmaster@example.com' . '\r\n';

                    if(mail($to, $subject, $message, $headers)){ 
                        mysqli_query($con,$sql);
                        header('Location: login.php');
                    }
            }
        }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Registrtion</title>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
   
        <style>
            .cardshadow{
                box-shadow: 15px 15px 20px black;
            }
            .eye{
                position:absolute;
            }
            .field{
                margin-right:50px;
            }
            img {
                width: 90%;
                height: auto;
            }
            html, body {
                margin: 0;
            }
            body {
                /* position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%); */
            }
            .box {
                width: 100%;
                background: white;
                /* background: #73C8A9;
                background: -webkit-linear-gradient(to right, #373B44, #73C8A9);
                background: linear-gradient(to right, #373B44, #73C8A9); */
                
            }
            .container {
                width:100%;
                height: auto; /* adjust this to the desired height */
                margin: 0 auto; /* center horizontally */
                margin-top: 0; /* flush with top edge */
                margin-bottom: 0; /* flush with bottom edge */
            }
            .heading{
                font-size: 45px;
                background: -webkit-linear-gradient(#00d4ff, #001875);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
    </style>
</head>
<body class="box">
            <div class="container p-5 ">    
                <div class="row m-5 mt-0">
                    <div class="row mb-0 bg-">
                        <p class="text-uppercase text-primary text-center fw-bold heading">Create Account</p>
                    </div>
                    <div class="col">
                        <img class='m-5' src="assets/image1.png" alt="">
                    </div>
                    <div class="col pt-5">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-xlg-middle">
                            <form id="form" class="p-5 ps-3 pb-0 me-5 pt-3 w-100" method="post" action="#">
                                <div class="form-floating mb-3 text-primary ">
                                    <input class="form-control" type="text" name="name" id="name" value="<?php echo $name_value; ?>" placeholder="Name">
                                    <label class="form-label " for="name">Name </label>
                                    <span class="text-danger"><?php echo $nameErr; ?></span>
                                </div>
                                <div class="form-floating mb-3 text-primary">
                                    <input class="form-control" type="tel" name="phone" id="phone" value="<?php echo $phone_value; ?>" placeholder="Phone">
                                    <label class="form-label" for="phone">Phone</label>
                                    <span class="text-danger"><?php echo $phErr; ?></span>
                                </div>
                                <div class="form-floating mb-3 text-primary">
                                    <input class="form-control" type="email" name="email" id="email" value="<?php echo $email_value; ?>" placeholder="Email">
                                    <label class="form-label" for="email">Email </label>
                                    <span class="text-danger"><?php echo $emailErr; ?></span>
                                </div>
                                <div class="form-floating mb-3 text-primary">
                                    <input class="form-control" type="password" name="pass" id="password" value="<?php echo $pass_value; ?>" placeholder="Password">
                                    <label class="form-label" for="password">Password</label>
                                    <span class="text-danger"><?php echo $passErr; ?></span>
                                </div>
                                <div class="form-floating mb-2 text-primary">
                                    <input class="form-control " type="password" name="pass2" id="confirm_password" value="<?php echo $pass2_value; ?>" placeholder="Confirm Password" >
                                    <label class="form-label" for="password2">Confirm Password </label>
                                    <span class="text-danger"><?php echo $passmatchErr; ?></span>
                                </div>
                                <div class="mb-4">
                                    <input type="checkbox" onclick="myFunction();">&nbsp;&nbsp;Show Password
                                </div>
                                <div class="d-grid gap-2 col-6 mx-auto pt-0">
                                    <input type="submit" class="btn btn-outline-primary btn-lg fw-bold py-1 text-uppercase" name="submit" value="Register">
                                </div>
                                <p class="text-center text-muted mt-1 mb-0">Have already an account? 
                                    <a href="login.php" class="fw-bold text-body"><u>Login here</u></a>
                                </p>
                            </form>
                        </div>
                    </div>
                    <hr class='text-info' style="border: 2px solid">
                </div>
            </div>
            
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

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