<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Ptk_terdaftar extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ptk_terdaftar';
    protected $primaryKey = 'ptk_terdaftar_id';
    public function ptk(){
        return $this->hasOne('App\Models\Ptk', 'ptk_id', 'ptk_id');
    }
}
