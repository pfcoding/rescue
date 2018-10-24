<?php

namespace App;

use FileUpload;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $casts = [
        'id' => 'int',
        'types' => 'array'
    ];

    protected $fillable = [
        'name',
        'types'
    ];

}
