<?php
    class Token{
        static function Sign($payload, $key, $expire=null){
            //header 
            $header = ['algo'=>'HS256', 'type'=>'JWT'];
            if($expire){
                $header['expire'] = time() + $expire;
                // echo $header['expire'];
            }
            // print_r($header);
            $header_encoded = base64_encode(json_encode($header));

            //Payload
            $payload_encoded = base64_encode(json_encode($payload));

            //signature 
            $signature = hash_hmac('SHA256', $header_encoded.$payload_encoded, $key);
            $signature_encoded = base64_encode($signature);

            return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
        }

        static function Verify($token, $key){
            //Break token part
            $token_parts = explode('.', $token);

            //Generate signature
            $signature = base64_encode((hash_hmac('SHA256', $token_parts[0].$token_parts[1], $key)));

            //Get header
            $header = json_decode(base64_decode($token_parts[0]), true);

            //Verify token expire
            if(isset($header['expire'])){
                if($header['expire'] < time()){
                    // echo $header['expire'];
                    echo "expired!!";
                    return false;
                }
            }

            //Verify signature
            if($signature!=$token_parts[2]){
                echo "Invalid!!";
                return false;
            }

            //Get payload
            $payload = json_decode(base64_decode($token_parts[1]),true);

            return $payload;

        }
    }

?>