<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registrasi_peserta_didik extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'registrasi_peserta_didik';
	protected $primaryKey = 'registrasi_id';
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id')->where('soft_delete', 0);;
    }
	public function peserta_didik(){
		return $this->hasOne('App\Peserta_didik', 'peserta_didik_id', 'peserta_didik_id')->where('soft_delete', 0);;
    }
}
