<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Anggota_rombel extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'anggota_rombel';
    protected $primaryKey = 'anggota_rombel_id';
    public function rombongan_belajar(){
        return $this->hasOne('App\Models\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function peserta_didik(){
        return $this->hasOne('App\Models\Peserta_didik', 'peserta_didik_id', 'peserta_didik_id');
    }
}
