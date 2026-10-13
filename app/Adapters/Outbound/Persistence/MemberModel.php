<?php

namespace App\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class MemberModel extends Model
{
    protected $table = 'library.member';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];
}
