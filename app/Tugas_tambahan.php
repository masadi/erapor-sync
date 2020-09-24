<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Tugas_tambahan extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'tugas_tambahan';
	protected $primaryKey = 'ptk_tugas_tambahan_id';
}
