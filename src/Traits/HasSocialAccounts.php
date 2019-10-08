<?php

namespace AllanChristian\SocialPassport\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use AllanChristian\SocialPassport\Models\SocialAccount;

trait HasSocialAccounts
{
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'owner_id');
    }
}
