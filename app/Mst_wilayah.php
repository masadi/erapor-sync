<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Mst_wilayah extends Model
{
	use SoftDeletes;
	protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
	protected $keyType = 'string';
	protected $table = 'ref.mst_wilayah';
	protected $primaryKey = 'kode_wilayah';
	protected $guarded = [];
	public function wilayah(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'mst_kode_wilayah');
    }
	public function parrent_recursive(){
		return $this->wilayah()->with('parrent_recursive');
	}
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'kode_wilayah', 'kode_wilayah');
    }
}
