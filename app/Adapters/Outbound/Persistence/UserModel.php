<?php

namespace App\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'library."user"';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['password_hash'];
}
