<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Pembelajaran extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'pembelajaran';
    protected $primaryKey = 'pembelajaran_id';
    public function rombongan_belajar(){
        return $this->hasOne('App\Models\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function ptk_terdaftar(){
        return $this->hasOne('App\Models\Ptk_terdaftar', 'ptk_terdaftar_id', 'ptk_terdaftar_id')->with('ptk');
    }
}
