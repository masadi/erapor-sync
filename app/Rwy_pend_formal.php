<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rwy_pend_formal extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
	protected $table = 'rwy_pend_formal';
	protected $primaryKey = 'riwayat_pendidikan_formal_id';
}
