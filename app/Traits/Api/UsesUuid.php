<?php

namespace App\Traits\Api;
use Illuminate\Support\Str;

trait UsesUuid
{
    /**
     * The "booted" method of the model.
    */
    protected static function bootUsesUuid(): void
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
