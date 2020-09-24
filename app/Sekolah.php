<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    public $incrementing = false;
	protected $table = 'sekolah';
	protected $primaryKey = 'sekolah_id';
	protected $keyType = 'string';
	public function wilayah(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'kode_wilayah');
    }
	public function jurusan_sp(){
		return $this->hasMany('App\Jurusan_sp', 'sekolah_id', 'sekolah_id')->where('soft_delete', 0);
    }
	public function kepala_sekolah(){
		return $this->hasOneThrough(
            'App\Ptk',
            'App\Ptk_terdaftar',
            'sekolah_id', // Foreign key on Ptk_terdaftar table...
            'ptk_id', // Foreign key on Ptk table...
            'sekolah_id', // Local key on sekolah table...
            'ptk_id' // Local key on Ptk_terdaftar table...
        );
		//return $this->hasOneThrough('App\Ptk', 'App\Ptk_terdaftar');
	}
	public function ptk(){
		return $this->hasManyThrough(
            'App\Ptk',
            'App\Ptk_terdaftar',
            'sekolah_id', // Foreign key on Ptk_terdaftar table...
            'ptk_id', // Foreign key on Ptk table...
            'sekolah_id', // Local key on sekolah table...
            'ptk_id' // Local key on Ptk_terdaftar table...
        );
	}
}
