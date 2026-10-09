<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasUuids;

    protected $table = 'transactions';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'account_origin_id',
        'account_destination_id',
        'node_id',
        'amount',
        'type',
        'idempotency_key',
        'created_at',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function originAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'account_origin_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'account_destination_id');
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'node_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}