<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Peserta_didik extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'peserta_didik';
    protected $primaryKey = 'peserta_didik_id';
    public function registrasi_peserta_didik(){
        return $this->hasOne('App\Models\Registrasi_peserta_didik', 'peserta_didik_id', 'peserta_didik_id');
    }
    public function wilayah(){
        return $this->hasOne('App\Models\Wilayah', 'kode_wilayah', 'kode_wilayah');
    }
    public function anggota_rombel(){
        return $this->hasOne('App\Models\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id');
        return $this->hasOneThrough(
            'App\Models\Rombongan_belajar',
            'App\Models\Anggota_rombel',
            'peserta_didik_id', // Foreign key on cars table...
            'rombongan_belajar_id', // Foreign key on owners table...
            'peserta_didik_id', // Local key on mechanics table...
            'rombongan_belajar_id' // Local key on cars table...
        );
    }
}
