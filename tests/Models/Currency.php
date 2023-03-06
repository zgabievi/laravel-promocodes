<?php

namespace Zorb\Promocodes\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    //
    protected $table = 'currencies';

    protected $fillable = [
        'value'
    ];
}
