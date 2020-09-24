<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rombongan_belajar extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'rombongan_belajar';
	protected $primaryKey = 'rombongan_belajar_id';
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id')->where('soft_delete', 0);
    }
	public function jurusan_sp(){
		return $this->hasOne('App\Jurusan_sp', 'jurusan_sp_id', 'jurusan_sp_id')->where('soft_delete', 0);
    }
	public function wali_kelas(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id')->where('soft_delete', 0);
    }
	public function ruang(){
		return $this->hasOne('App\Ruang', 'id_ruang', 'id_ruang')->where('soft_delete', 0);
    }
}
