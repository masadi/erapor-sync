<?php

namespace App\Models;
class EncryptPassword {
    private $hash = "94bc6261-eaad-4a4d-8057-f2c5c7fc212d=94bc6261-eaad-4a4d-8057-f2c5c7fc212d";
    public $string;

    protected function getHash() {
        return $this->hash;
    }

    public function setString($string) {
        $this->string = $string;
    }

    public function doEncrypt() {
        $str = str_rot13($this->getHash());
        $token = $this->getHash();
        $key = substr($token,0,1).substr($token,10,11).substr($token,20,21);
        $str = $this->doCooking($this->string, 1, $key);
        return $str;
    }

    protected function doCooking($text, $crypt, $secret_key, $alg='aes-128-ctr') {
        $output = false;

        $secret_iv = $this->getHash();
        $key = hash('sha256', $secret_key,true);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_encrypt($text, $alg, $key, 0, $iv);
        return $output;
    }
}
