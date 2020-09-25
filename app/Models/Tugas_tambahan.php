<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Tugas_tambahan extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'tugas_tambahan';
    protected $primaryKey = 'ptk_tugas_tambahan_id';
    public function ptk(){
        return $this->hasOne('App\Models\Ptk', 'ptk_id', 'ptk_id');
    }
}
