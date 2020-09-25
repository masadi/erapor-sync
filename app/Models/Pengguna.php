<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;
class Pengguna extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletesBoolean;
    public const IS_DELETED = 'soft_delete';
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'man_akses.pengguna';
	protected $primaryKey = 'pengguna_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    public function role_pengguna(){
        return $this->hasOne('App\Models\Role_pengguna', 'pengguna_id', 'pengguna_id');
    }
    public function sekolah(){
        return $this->hasOne('App\Models\Sekolah', 'sekolah_id', 'sekolah_id');
    }
}
