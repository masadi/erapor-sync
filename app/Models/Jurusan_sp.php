<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Jurusan_sp extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'jurusan_sp';
    protected $primaryKey = 'jurusan_sp_id';
    public function jurusan(){
        return $this->hasOne('App\Models\Jurusan', 'jurusan_id', 'jurusan_id');
    }
}
