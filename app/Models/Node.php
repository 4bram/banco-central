<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Node extends Model
{
    use HasUuids;

    protected $table = 'nodes';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'type',
        'location',
        'cash_available',
        'status',
        'created_at',
    ];

    protected $casts = [
        'cash_available' => 'decimal:2',
        'created_at'     => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────
    public function credentials(): HasMany
    {
        return $this->hasMany(NodeCredential::class, 'node_id');
    }

    public function responsibles(): HasMany
    {
        return $this->hasMany(NodeResponsible::class, 'node_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class, 'created_by_node_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'node_id');
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'node_id');
    }

    public function atmAuthAttempts(): HasMany
    {
        return $this->hasMany(AtmAuthAttempt::class, 'node_id');
    }

    // ─── Scopes útiles ────────────────────────────────
    public function scopeBranches($query)
    {
        return $query->where('type', 'branch');
    }

    public function scopeAtms($query)
    {
        return $query->where('type', 'atm');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}