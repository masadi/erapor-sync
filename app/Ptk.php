<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Ptk extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'ptk';
	protected $primaryKey = 'ptk_id';
	public $timestamps = false;
	public function tugas_tambahan(){
		return $this->hasOne('App\Tugas_tambahan', 'ptk_id', 'ptk_id')->where('soft_delete', 0);
    }
	public function wilayah(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'kode_wilayah');
    }
	public function ptk_terdaftar(){
		return $this->hasOne('App\Ptk_terdaftar', 'ptk_id', 'ptk_id')->where('soft_delete', 0);
    }
	public function rwy_pend_formal(){
		return $this->hasMany('App\Rwy_pend_formal', 'ptk_id', 'ptk_id')->where('soft_delete', 0)->whereNotNull('gelar_akademik_id')->where('gelar_akademik_id', '!=', 99999);
    }
}
