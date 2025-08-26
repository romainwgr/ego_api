<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class RefreshToken extends Model
{
    protected $table = 'refresh_tokens'; 

    protected $fillable = [
        'user_id','token_id','token_hash',
        'expires_at','user_agent','ip','revoked_at',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        // avec $casts ['expires_at' => 'datetime'] c’est déjà un Carbon
        return $this->expires_at?->isPast() ?? true; // si null => considéré expiré
    }

    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }

    // pratique :
    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }

    // et un scope utile :
    public function scopeValid($q)
    {
        return $q->whereNull('revoked_at')->where('expires_at', '>', now());
    }

}
