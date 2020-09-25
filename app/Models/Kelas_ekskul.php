<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Kelas_ekskul extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'kelas_ekskul';
    protected $primaryKey = 'id_kelas_ekskul';
    public function rombongan_belajar(){
        return $this->hasOne('App\Models\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
}
