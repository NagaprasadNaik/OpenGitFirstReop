<?php
require 'Token.php';

$key = 'key';

// $token = Token::Sign(['id'=>'thisIsId'], $key, 2*60);
// echo $token;

$token = 'eyJhbGdvIjoiSFMyNTYiLCJ0eXBlIjoiSldUIiwiZXhwaXJlIjoxNjg0MTUzNjAzfQ==.eyJpZCI6InRoaXNJc0lkIn0=.OTUyYWJmOTE1N2EwZmNiZThiNTFmYzY2NWU4OTkzODVmMTk0YWIyN2U3NDE1ODI5MjlmOTk1Y2JkNjQwODU3Mg==';

$value = Token::Verify($token, $key);

print_r($value);

 ?>