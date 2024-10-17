<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property string $title
 * @property string $type
 * @property string $isbn
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    // Define which fields can be mass-assigned when creating or updating a Book
    protected $fillable = ['uuid', 'title', 'type', 'collector_id', 'isbn'];

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
            'isbn' => 'string',
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
        self::creating(function ($book) {
            // Generate a new UUID for the 'uuid' field
            $book->uuid = (string) Str::uuid();
        });
    }

    /**
     * Define the relationship between a Book and a Collector.
     * A Book belongs to a single Collector.
     *
     * @return BelongsTo<Collector, Book>
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(Collector::class);
    }
}
