<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    public $incrementing = false;
	protected $table = 'ref.semester';
	protected $primaryKey = 'semester_id';
	protected $guarded = [];
	public function tahun_ajaran(){
		return $this->hasOne('App\Models\Tahun_ajaran', 'tahun_ajaran_id', 'tahun_ajaran_id')->whereNull('expired_date');
	}
}
