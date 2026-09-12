<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'position',
        'phone_number',
        'hire_date',
        'employment_status',
        'pin_code'
    ];

    protected $hidden = [
        'pin_code'
    ];
}