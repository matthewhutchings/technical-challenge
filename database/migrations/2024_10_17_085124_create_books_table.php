<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This method will create the books table in the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            // UUID field for the book, acting as the primary key
            $table->uuid('uuid')->primary();

            $table->string('isbn')->nullable();
            // Title of the book, a required string field
            $table->string('title');

            // Type of the book, which could be Fiction, Non-Fiction, Technical, or Self-Help
            $table->enum('type', ['Fiction', 'Non-Fiction', 'Technical', 'Self-Help']);

            // Foreign key linking the book to a collector, with cascading delete
            $table->foreignId('collector_id')->constrained()->onDelete('cascade');

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
