<?php

namespace App\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class LoanItemModel extends Model
{
    protected $table = 'library.loan_item';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];
}
