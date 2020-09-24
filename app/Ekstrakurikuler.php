<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
	protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'kelas_ekskul';
	protected $primaryKey = 'id_kelas_ekskul';
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id')->where('soft_delete', 0);
    }
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id')->where('soft_delete', 0);
    }
}
