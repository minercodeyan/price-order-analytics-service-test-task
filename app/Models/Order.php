<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    // Отключаем автоматические timestamps Laravel
    public $timestamps = false;

    protected $fillable = [
        'hash', 'user_id', 'token', 'number', 'status', 'step',
        'client_name', 'client_surname', 'email', 'company_name',
        'currency', 'cur_rate', 'total_amount', 'discount',
        'create_date', 'update_date', 'deleted_at'
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'update_date' => 'datetime',
        'deleted_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'cur_rate' => 'decimal:4',
    ];

    // Переопределяем методы для SoftDeletes
    const DELETED_AT = 'deleted_at';
}
