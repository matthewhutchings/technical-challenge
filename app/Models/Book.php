<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Book extends Model
{
    use HasFactory;

    // Define which fields can be mass-assigned when creating or updating a Book
    protected $fillable = ['uuid', 'title', 'type', 'collector_id'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    /**
     * The boot method is called automatically when the model is initialized.
     * Here, we're hooking into the creating event to automatically generate a UUID for the Book
     * before it gets saved to the database.
     */
    public static function boot()
    {
        parent::boot();

        // Listen to the 'creating' event and generate a UUID when a new book is being created
        static::creating(function ($book) {
            // Generate a new UUID for the 'uuid' field
            $book->uuid = (string) Str::uuid();
        });
    }

    /**
     * Define the relationship between a Book and a Collector.
     * A Book belongs to a single Collector.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }
}
