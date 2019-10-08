<?php

namespace AllanChristian\SocialPassport\Traits;


use AllanChristian\SocialPassport\Models\SocialAccount;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSocialAccounts
{
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'owner_id');
    }
}
