<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pembelajaran extends Model
{
	protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'pembelajaran';
	protected $primaryKey = 'pembelajaran_id';
	public function ptk_terdaftar(){
		return $this->hasOne('App\Ptk_terdaftar', 'ptk_terdaftar_id', 'ptk_terdaftar_id')->where('soft_delete', 0);
    }
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id')->where('soft_delete', 0);
    }
}
