<?php

namespace AllanChristian\SocialPassport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AllanChristian\SocialPassport\Facades\SocialPassport;

class SocialAccount extends Model
{
    protected $fillable = [
        'provider_name',
        'provider_id',
        'owner_id',
    ];

    /**
     * Return user that owns this Social account.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(SocialPassport::getAuthProviderModel(), 'owner_id');
    }
}
