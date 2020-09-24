<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anggota_akt_pd extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'anggota_akt_pd';
	protected $primaryKey = 'id_ang_akt_pd';
	public function registrasi_peserta_didik(){
		return $this->hasOne('App\Registrasi_peserta_didik', 'registrasi_id', 'registrasi_id')->where('soft_delete', 0);
    }
}
