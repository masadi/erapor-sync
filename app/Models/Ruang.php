<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Ruang extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ruang';
    protected $primaryKey = 'id_ruang';
}
