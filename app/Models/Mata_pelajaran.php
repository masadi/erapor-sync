<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Mata_pelajaran extends Model
{
    use SoftDeletes;
    protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ref.mata_pelajaran';
    protected $primaryKey = 'mata_pelajaran_id';
    public function jurusan_smk(){
        return $this->hasOne('App\Models\Jurusan', 'jurusan_id', 'jurusan_id')->where('untuk_smk', 1);
    }
}
