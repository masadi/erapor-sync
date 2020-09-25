<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role_pengguna extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'man_akses.role_pengguna';
    protected $primaryKey = 'id_role_pengguna';
}
