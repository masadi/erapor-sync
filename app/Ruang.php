<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'ruang';
	protected $primaryKey = 'id_ruang';
}
