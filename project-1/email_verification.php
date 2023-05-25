<?php

    @include('db.php');

    $hashed_email = $_GET['email'];

    //decrypting email
    function decrypt($data){
        $chipering = 'AES-128-CTR';
        $data = base64_decode($data);
        $option = 0;
        $env_iv = '1234567890123456';
        $env_key = 'secretKey';
        $data = openssl_decrypt($data, $chipering, $env_key, $option, $env_iv);

        while ($error = openssl_error_string()) {
            echo "OpenSSL Error: " . $error . "<br>";
        }
        return $data;
    }

    if($hashed_email){
        $email = decrypt($hashed_email);
    }else{
        $email = false;
    }

    if($email){
        try{
            $sql = "UPDATE user SET mail_status = 1 WHERE email = '$email' ";
            $result = mysqli_query($con, $sql);
        }catch(Exception $e){
            ?>
            <script>
                alert('Please try after some time!');
                window.location = "login.php";
            </script>
        <?php
        }
        if($result == true){
            ?>
                <script>
                    alert('Email verified successfully. Please Login!');
                    window.location = "login.php";
                </script>
            <?php
        }
    }else{
        ?>
        <script>
            alert('Invaild Email!. Please verify your email address!');
            window.location = "login.php";
        </script>
    <?php
    }
?>