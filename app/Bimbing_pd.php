<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bimbing_pd extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'bimbing_pd';
	protected $primaryKey = 'id_bimb_pd';
	public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id')->where('soft_delete', 0);
    }
}
