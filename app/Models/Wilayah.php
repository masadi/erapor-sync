<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Wilayah extends Model
{
    use SoftDeletes;
    protected $dates = ['expired_date'];
    const DELETED_AT = 'expired_date';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ref.mst_wilayah';
    protected $primaryKey = 'kode_wilayah';
    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'mst_kode_wilayah', 'kode_wilayah');
    }
    public function parrentRecursive()
    {
        return $this->parent()->with('parrentRecursive');
    }
}
