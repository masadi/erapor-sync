<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Kurikulum extends Model
{
    use SoftDeletes;
    protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ref.kurikulum';
    protected $primaryKey = 'kurikulum_id';
    public function jenjang_smk(){
        return $this->hasOne('App\Models\Jenjang_pendidikan', 'jenjang_pendidikan_id', 'jenjang_pendidikan_id')->where('jenjang_pendidikan_id', 6);
    }
}
