<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Anggota_akt_pd extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'anggota_akt_pd';
    protected $primaryKey = 'id_ang_akt_pd';
    public function registrasi_peserta_didik(){
        return $this->hasOne('App\Models\Registrasi_peserta_didik', 'registrasi_id', 'registrasi_id');
    }
}
