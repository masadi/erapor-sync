<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Jenjang_pendidikan extends Model
{
    use SoftDeletes;
    protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ref.jenjang_pendidikan';
    protected $primaryKey = 'jenjang_pendidikan_id';
}
