<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Rombongan_belajar extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'rombongan_belajar';
    protected $primaryKey = 'rombongan_belajar_id';
    public function ptk(){
        return $this->hasOne('App\Models\Ptk', 'ptk_id', 'ptk_id');
    }
    public function kelas_ekskul(){
        return $this->hasOne('App\Models\Kelas_ekskul', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function jurusan_sp(){
        return $this->hasOne('App\Models\Jurusan_sp', 'jurusan_sp_id', 'jurusan_sp_id');
    }
}
