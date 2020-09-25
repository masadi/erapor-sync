<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Akt_pd extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'akt_pd';
    protected $primaryKey = 'id_akt_pd';
    public function anggota_akt_pd(){
        return $this->hasMany('App\Models\Anggota_akt_pd', 'id_akt_pd', 'id_akt_pd');
    }
    public function bimbing_pd(){
        return $this->hasMany('App\Models\Bimbing_pd', 'id_akt_pd', 'id_akt_pd');
    }
}
