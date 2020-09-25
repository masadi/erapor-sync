<?php

namespace App;

class HelperModel
{
    public static function prepare_send($str){
		return rawurlencode(base64_encode(gzcompress(self::encryptor(serialize($str)))));
	}
	public static function prepare_receive($str){
		return unserialize(self::decryptor(gzuncompress(base64_decode(rawurldecode($str)))));
	}
	public static function encryptor($str){
		return $str;
	}
	public static function decryptor($str){
		return $str;
	}
}
