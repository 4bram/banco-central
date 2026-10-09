<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use HasUuids;

    protected $table = 'cash_movements';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'node_id',
        'amount',
        'type',
        'previous_cash',
        'new_cash',
        'created_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'previous_cash' => 'decimal:2',
        'new_cash'      => 'decimal:2',
        'created_at'    => 'datetime',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'node_id');
    }
}