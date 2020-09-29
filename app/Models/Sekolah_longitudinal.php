<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Sekolah_longitudinal extends Model
{
    use SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'sekolah_longitudinal';
    protected $primaryKey = ['sekolah_id', 'semester_id'];
}
