<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeCredential extends Model
{
    use HasUuids;

    protected $table = 'node_credentials';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'node_id',
        'key_hash',
        'status',
        'created_at',
        'revoked_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'node_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}