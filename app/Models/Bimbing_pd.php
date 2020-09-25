<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Bimbing_pd extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'bimbing_pd';
    protected $primaryKey = 'id_bimb_pd';
    public function ptk(){
        return $this->hasOne('App\Models\Ptk', 'ptk_id', 'ptk_id');
    }
}
