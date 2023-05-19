<?php

@include 'db.php'; 

session_start();

//For sweet alert and alert message
$_SESSION['status'] = false;
$_SESSION['flag'] = false;

if(isset($_POST['submit'])){
   
   $email = $_POST['email'];

   // $pass = md5($_POST['password']);
   $pass = $_POST['password'];

   $con = mysqli_connect($servername, $username, $password, $database);
   
   $select = "SELECT * FROM user WHERE email = '$email' && password = '$pass' && mail_status = 1 ";

   $result = mysqli_query($con, $select);


   if(mysqli_num_rows($result) > 0){

      $row = mysqli_fetch_array($result);
     
      $status = "UPDATE user SET status = 1 WHERE email = '$email' && password = '$pass'";
      $result2 = mysqli_query($con, $status);

      if($row['role'] == 'admin'){
         $_SESSION['logged'] = true;
         $_SESSION['email'] = $row['email'];
         $_SESSION['role'] = $row['role'];
         header('location:admin.php');
      }elseif($row['role'] == 'user'){
         $_SESSION['logged'] = true;
         $_SESSION['email'] = $row['email'];
         header('location:profile.php');
      }
   }else{
      $error[] = 'incorrect email or password!';
      if(isset($_SESSION['login_attempts'])){
         $_SESSION['login_attempts']++;
      }else{
         $_SESSION['login_attempts'] = 1;
      }
   }
}
if(isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3){
   $_SESSION['locked'] = time() + 29;
   unset($_SESSION['login_attempts']);
   // header('location:user_page.php');
   echo "<p>Please wait for 30 seconds</p>";
   // $error[] = 'Please wait for 10 seconds!';
   exit();
}
if(isset($_SESSION['locked']) && $_SESSION['locked'] > time()){
   echo "<p>Please wait for " . ($_SESSION['locked'] - time()) . " seconds</p>";
   exit();
}else{
   unset($_SESSION['Blocked']);
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
   <link rel="stylesheet"  type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="http://use.fontawesome.com/releases/v5.8.1/css/all.css" >
   <link rel="stylesheet"  type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>  
   <div class="form-container">
      <form action="" method="post">
            <!-- header og login  -->
            <h3>login</h3>
            <?php
            if(isset($error)){

               foreach($error as $error){
                  echo '<span class="error-msg">'.$error.'</span>';
               };
            };
            ?>
            
         <!-- login altribute of email and password -->
            <input type="text" name="email" required placeholder="Email">
            <input type="password" id="password" name="password" required placeholder="enter your password">
         <!-- <span>
            <i class="fa fa-eye" aria-hidden="true" id="eye" >
            </i>
         </span> -->
         <div> 
            <input type="submit" name="submit" value="login now" class="form-btn">
            </div>
            <p><a href="#">forget password?</a></p>
            <p>don't have an account? <a href="registration.php">Register now</a></p>
         </div>      
      </form>
   </div>
</body>
</html>
