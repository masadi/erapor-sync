<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Ptk extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ptk';
    protected $primaryKey = 'ptk_id';
    public function ptk_terdaftar(){
        return $this->hasOne('App\Models\Ptk_terdaftar', 'ptk_id', 'ptk_id')->whereNull('jenis_keluar_id');
    }
    public function wilayah(){
        return $this->hasOne('App\Models\Wilayah', 'kode_wilayah', 'kode_wilayah');
    }
    public function rwy_pend_formal(){
        return $this->hasMany('App\Models\Rwy_pend_formal', 'ptk_id', 'ptk_id');
    }
}
