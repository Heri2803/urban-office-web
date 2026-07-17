<?php

namespace App\Traits;

use App\Models\Scopes\LocationScope;

trait HasLocationScope
{
    /**
     * The "booted" method of the trait.
     *
     * @return void
     */
    protected static function bootHasLocationScope()
    {
        static::addGlobalScope(new LocationScope);
    }
}
