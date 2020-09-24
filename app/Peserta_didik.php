<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peserta_didik extends Model
{
	protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'peserta_didik';
	protected $primaryKey = 'peserta_didik_id';
	public $timestamps = false;
	public function registrasi_peserta_didik(){
		return $this->hasOne('App\Registrasi_peserta_didik', 'peserta_didik_id', 'peserta_didik_id')->where('soft_delete', 0);
    }
	public function wilayah(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'kode_wilayah');
    }
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id')->where('soft_delete', 0);
    }
	public function rombongan_belajar(){
		return $this->hasOneThrough(
            'App\Rombongan_belajar',
            'App\Anggota_rombel',
            'peserta_didik_id', // Foreign key on Anggota_rombel table...
            'rombongan_belajar_id', // Foreign key on Rombongan_belajar table...
            'peserta_didik_id', // Local key on Peserta_didik table...
            'rombongan_belajar_id' // Local key on Anggota_rombel table...
        )->where('anggota_rombel.soft_delete', 0)->where('rombongan_belajar.soft_delete', 0);
	}
}
