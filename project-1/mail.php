  <?php
  session_start();

  if(!$_SESSION['logged']){
    header('location : login.php');
}

  $reply_to = $_SESSION['email'];

  function testinput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

    $email = $subject = $message = "";
    $email_err = $subject_err = $message_err ="";
    $email_value = $subject_value = $message_value = "";

    if(isset($_POST['send'])){
      $email=$_POST['email'];
      $subject=$_POST['subject'];
      $message=$_POST['message'];

      //email validation
      if(empty($email)){
        $email_err = "Enter the recipient email";
        $subject_value = $subject;
        $message_value = $message ;
        echo $message_value;
      }else{
        $email =testinput($email);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
          $email_err="Enter valid Email address";
          $subject_value = $subject;
          $message_value = $message;
        }
      }

      if(empty($subject)){
        $subject_err = "Subject cannot be empty";
        $email_value = $email;
        $message_value = $message;
      }
      
      if(empty($message)){
        $message_err = "Message cannot be empty";
        $email_value = $email;
        $subject_value = $subject;
      }

      if (empty($email_err) && empty($subject_err) && empty($message_err)){
        $to = $email;
        $sub =$subject;
        $mes = $message;
        $headers = 'From: openmail12345678@gmail.com'. "\r\n" ;

        if(mail($to,$sub,$mes,$headers)){
          ?>   
            <script>
              window.addEventListener('load', function(){
                swal({
                  type:"success",
                  title: "Email Sent Successfully",
                })
              });
            </script>
          <?php
        }
    }
  }
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>  
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<title>Mail</title>
    <style>
      .mail{
        border-radius: 30px;
      }
      .navbar-dark .navbar-nav  .nav-link:hover{
            color: orange;
        }
    </style>
    </style>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>


<body class="bg-light">
      <!-- navbar start -->
      <nav class="navbar navbar-expand navbar-dark bg-dark pt-4 pb-4">
    <div class="container">
    <a href="profile.php" class="navbar-brand fw-bold fs-4 border border-1 rounded p-1">Profile</a>
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="profile.php" class="nav-link active ms-2 me-2 fw-bold" >Profile</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link active ms-2 me-2 fw-bold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Update
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="profile_update.php"  name="profile">Profile</a></li>
            <li><a class="dropdown-item" href="password_update.php"  name="password">Password</a></li>
          </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link active ms-2 me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal">Logout</a>
            <div class="modal fade" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                           <p class="fw-bold">Confirm Logout</p> 
                        </div>
                        <div class="modal-body mt-2 mb-2 ps-5 ">
                           <p class="fs-5">Are you sure! you want to logout?</p> 
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

<div class="">
    <div class="container my-3 p-4 w-50 mail bg-white shadow-lg">
      <p class="text-uppercase text-primary text-center fw-bold fs-2">Send mail</p>
        <form class="" method="post" action="#">
          <div class="mb-3">
            <label for="email" class="form-label text-primary fw-bold">To</label>
            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email_value; ?>">
            <span class="text-danger"><?php echo $email_err; ?></span>
          </div>
          <div class="mb-3">
            <label for="Subject" class="form-label text-primary fw-bold">Subject</label>
            <input type="text" class="form-control" id="Subject" name="subject" value="<?php echo $subject_value; ?>">
            <span class="text-danger"> <?php echo $subject_err; ?></span>
          </div>
          <div class="mb-3">
            <p class="form-label"><label for="message" class="form-label text-primary fw-bold">Message</label></p>
            <textarea class="form-control" rows="5" name="message"><?php echo $message_value; ?></textarea>
            <span class="text-danger"> <?php echo $message_err; ?></span>
          </div >
          <div class="text-center">
            <button class="btn btn-primary btn-lg px-5 mt-3" type="submit" name="send">Send</button>
          </div>
        </form>
    </div>
</div>
</body>
</html>