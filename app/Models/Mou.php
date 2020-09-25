<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Mou extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'mou';
    protected $primaryKey = 'mou_id';
    public function akt_pd(){
        return $this->hasMany('App\Models\Akt_pd', 'mou_id', 'mou_id');
    }
}
