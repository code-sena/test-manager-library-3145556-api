<?php

namespace App\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class LoanModel extends Model
{
    protected $table = 'library.loan';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'loaned_at' => 'datetime',
    ];
}
