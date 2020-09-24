<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anggota_rombel extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'anggota_rombel';
	protected $primaryKey = 'anggota_rombel_id';
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id')->where('soft_delete', 0);
    }
}
