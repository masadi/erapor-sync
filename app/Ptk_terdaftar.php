<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Ptk_terdaftar extends Model
{
	public $incrementing = false;
	protected $keyType = 'string';
    protected $table = 'ptk_terdaftar';
	protected $primaryKey = 'ptk_terdaftar_id';
	public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
    }
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
}
