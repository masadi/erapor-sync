<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Mata_pelajaran_kurikulum extends Model
{
    use SoftDeletes;
    protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ref.mata_pelajaran_kurikulum';
    protected $primaryKey = ['kurikulum_id', 'mata_pelajaran_id', 'tingkat_pendidikan_id'];
}
