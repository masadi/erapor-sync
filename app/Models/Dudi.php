<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Dudi extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'dudi';
    protected $primaryKey = 'dudi_id';
    public function mou(){
        return $this->hasOne('App\Models\Mou', 'dudi_id', 'dudi_id');
    }
}
