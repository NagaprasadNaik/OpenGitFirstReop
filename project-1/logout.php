<?php 
    @include 'db.php';

    session_start();

    //importing files for jwt-token
    require('php-jwt/src/BeforeValidException.php');
    require('php-jwt/src/CachedKeySet.php');
    require('php-jwt/src/ExpiredException.php');
    require('php-jwt/src/JWK.php');
    require('php-jwt/src/JWT.php');
    require('php-jwt/src/Key.php');
    require('php-jwt/src/SignatureInvalidException.php');

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    //Decoding token
    $token = $_SESSION['token'];
    try{
        //retutns a stdClass object
        $row = JWT::decode($token, new Key('secretKey', 'HS256'));
    }catch(Exception $e){
        ?>
            <script>
                alert('Server not responding. Please try after some time');
                window.location = 'profile.php';
            </script>
        <?php
    }

    //Extracting data from stdClass object and initilizing to variables
    $id = $row->id;
    $email = $row->email;


    try{
        $status = "UPDATE user SET otp_attempts = 0 WHERE email = '$email'";
        $result2 = mysqli_query($con, $status);
    }catch(Exception $e){
        ?>
            <script>
                alert('Server not responding. Please try after some time');
                window.location = 'profile.php';
            </script>
        <?php
    }

    $_SESSION['logged'] = false;

    session_unset();
    session_destroy();

    header('location: login.php');
    exit;
?>