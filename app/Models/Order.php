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

    const DELETED_AT = 'deleted_at';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class, 'order_id');
    }


    public function management()
    {
        return $this->hasOne(OrderManagement::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }


    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }

    /**
     * Получить текст статуса
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            1 => 'Новый',
            2 => 'Оплачен',
            3 => 'Собран',
            4 => 'Отправлен',
            5 => 'Доставлен',
            6 => 'Отменен',
            default => 'Неизвестно',
        };
    }
}
