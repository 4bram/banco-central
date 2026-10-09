<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtmAuthAttempt extends Model
{
    use HasUuids;

    protected $table = 'atm_auth_attempts';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'account_id',
        'node_id',
        'success',
        'attempted_at',
    ];

    protected $casts = [
        'success'      => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'account_id');
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'node_id');
    }
}