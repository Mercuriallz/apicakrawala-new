<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Jwt {

    public static $leeway = 0;

    public function decode($jwt, $key, $verify = true) {

        $isErr = false;
        $msgErr = null;

        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            $isErr = true;
            $msgErr = 'Wrong number of segments';
        }

        list($headb64, $payloadb64, $cryptob64) = $tks;
        if (!$isErr) {
            if (null === ($header = $this->jsonDecode($this->urlsafeB64Decode($headb64)))) {
                $isErr = true;
                $msgErr = 'Invalid segment encoding';
            }
        }
        if (!$isErr) {
            if (null === $payload = $this->jsonDecode($this->urlsafeB64Decode($payloadb64))) {
                $isErr = true;
                $msgErr = 'Invalid segment encoding';
            }
        }
        if (!$isErr) {
            $sig = $this->urlsafeB64Decode($cryptob64);
            if ($verify) {
                if (empty($header->alg)) {
                    $isErr = true;
                    $msgErr = 'Empty algorithm';
                }
                if (!$isErr) {
                    $this->sign("$headb64.$payloadb64", $key);
    
                    if ($sig != $this->sign("$headb64.$payloadb64", $key)) {
                        $isErr = true;
                        $msgErr = 'Signature verfication failed '. $jwt;
                    }
                }
            }
        }

        if (!$isErr) {
            if (isset($payload->iat) && $payload->iat > (time() + self::$leeway)) {
                $isErr = true;
                $msgErr = 'Cannot handle token prior to iat ' . date(DateTime::ISO8601, $payload->iat);
            }
        }

        if (!$isErr) {
            if (isset($payload->exp) && (time() - self::$leeway) >= $payload->exp) {
                $isErr = true;
                $msgErr = 'Token Expired';
            }
        }
        
        /*
        if (isset($payload->nbf) && $payload->nbf > (time() + self::$leeway)) {
            $isErr=true;
            $msgErr='Cannot handle token prior to nbf ' . date(DateTime::ISO8601, $payload->nbf);
        }
        */
        
        if ($isErr) {
            return (object) [
                'STATUS' => 0,
                'MESSAGE' => $msgErr,
                'DATA' => new stdClass()
            ];
        } else {
            return (object) [
                'STATUS' => 1,
                'MESSAGE' => $msgErr,
                'DATA' => $payload
            ];
        }
    }

    public function encode($payload, $key) {
        $header = array(
            'typ' => 'jwt',
            'alg' => 'HS256'
        );

        $segments = array();
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
        $signing_input = implode('.', $segments);

        $signature = $this->sign($signing_input, $key);
        $segments[] = $this->urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    public function sign($msg, $key, $method = 'HS256') {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );

        return hash_hmac($methods[$method], $msg, $key, true);
    }

    public function jsonDecode($input) {
        $obj = json_decode($input);
        $errmsg = null;
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            $errmsg = $this->handleJsonError($errno);
        } elseif ($obj === null && $input !== 'null') {
            $errmsg = 'Null result with non-null input';
        }
        if (is_null($errmsg)) {
            return $obj;
        } else {
            return $errmsg;
        }
    } 

    public function jsonEncode($input) {
        $json = json_encode($input);
        $errmsg = null;
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            $errmsg = $this->handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            $errmsg = 'Null result with non-null input';
        }
        if (is_null($errmsg)) {
            return $json;
        } else {
            return $errmsg;
        }
    }

    public function urlsafeB64Decode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public function urlsafeB64Encode($input) {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private function handleJsonError($errno) {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        return isset($messages[$errno]) ? $messages[$errno] : 'Unknown JSON error: ' . $errno;
    }

}
