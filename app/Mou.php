<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mou extends Model
{
	protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'mou';
	protected $primaryKey = 'mou_id';
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id')->where('soft_delete', 0);
    }
	public function dudi(){
		return $this->hasOne('App\Dudi', 'dudi_id', 'dudi_id')->where('soft_delete', 0);
    }
	public function akt_pd(){
		return $this->hasMany('App\Akt_pd', 'mou_id', 'mou_id')->where('soft_delete', 0);
    }
}
