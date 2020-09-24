<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
	protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'dudi';
	protected $primaryKey = 'dudi_id';
	public function mou(){
		return $this->hasMany('App\Mou', 'dudi_id', 'dudi_id')->where('soft_delete', 0);
    }
}
