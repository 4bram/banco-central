<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAccount extends Model
{
    use HasUuids;

    protected $table = 'users_accounts';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'account_number',
        'holder_name',
        'pin_hash',
        'balance',
        'status',
        'created_by_node_id',
        'created_at',
    ];

    protected $hidden = [
        'pin_hash',   // nunca exponer el hash del PIN
    ];

    protected $casts = [
        'balance'    => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function createdByNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'created_by_node_id');
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_origin_id');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_destination_id');
    }

    public function atmAuthAttempts(): HasMany
    {
        return $this->hasMany(AtmAuthAttempt::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}