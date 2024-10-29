<?php

namespace App\Models;

use App\Enums\Api\FullCalendarEventStatus;
use App\Traits\Api\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullCalendarEvent extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'date',
        'status',
        'user_id'
    ];

    /**
     * Set the key type to string.
     *
     * @var string
     */
    protected $keyType = 'string'; // Specify that the key is a string

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;   // Prevents auto-incrementing IDs

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Interact with the event's date.
     */
    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->format('m-d-Y'),
        );
    }

}
