<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Akt_pd extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'akt_pd';
	protected $primaryKey = 'id_akt_pd';
	public function anggota_akt_pd(){
		return $this->hasMany('App\Anggota_akt_pd', 'id_akt_pd', 'id_akt_pd')->where('soft_delete', 0);
    }
	public function bimbing_pd(){
		return $this->hasMany('App\Bimbing_pd', 'id_akt_pd', 'id_akt_pd')->where('soft_delete', 0);
    }
}
