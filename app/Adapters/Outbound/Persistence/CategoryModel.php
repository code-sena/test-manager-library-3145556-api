<?php

namespace App\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'library.category';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];
}
