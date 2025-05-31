<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'account';

    protected $fillable = [
        'accType',
        'accName',
        'accAmount',
        'accDate',
    ];

    protected $casts = [
        'accDate' => 'datetime',
        'accAmount' => 'decimal:2',
        'accType' => 'string',
        'accName' => 'string',
    ];

    public $timestamps = true;

}
