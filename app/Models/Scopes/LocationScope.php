<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class LocationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only apply the location scope for regular admins
            if ($user->role === 'admin' && !empty($user->location_id)) {
                if ($model instanceof \App\Models\Location) {
                    $builder->where($model->getTable() . '.id', $user->location_id);
                } else {
                    $builder->where($model->getTable() . '.location_id', $user->location_id);
                }
            }
        }
    }
}
