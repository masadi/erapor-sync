<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Sekolah extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'sekolah';
    protected $primaryKey = 'sekolah_id';
    public function pengguna(){
        return $this->hasMany('App\Models\Pengguna', 'sekolah_id', 'sekolah_id');
    }
    public function jurusan_sp(){
        return $this->hasMany('App\Models\Jurusan_sp', 'sekolah_id', 'sekolah_id')->whereHas('jurusan', function($query){
            $query->where('untuk_smk', 1);
        });
    }
    public function ptk_terdaftar(){
        return $this->hasOne('App\Models\Ptk_terdaftar', 'sekolah_id', 'sekolah_id');
    }
    public function tugas_tambahan(){
        return $this->hasOne('App\Models\Tugas_tambahan', 'sekolah_id', 'sekolah_id');
    }
    public function wilayah(){
        return $this->hasOne('App\Models\Wilayah', 'kode_wilayah', 'kode_wilayah');
    }
}
