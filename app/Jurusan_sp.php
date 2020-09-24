<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurusan_sp extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'jurusan_sp';
	protected $primaryKey = 'jurusan_sp_id';
}
