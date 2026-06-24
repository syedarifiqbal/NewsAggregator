<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adding Cache layer on Article Repository will add unnecessary complexity to the project without much benefit 
     * Since we are supporting Filters it will required segnificant work to forget/add/manage the cache, 
     * So we are already optimizing the queries with indexes, it should be sufficient for now.
     * Adding indexes will help speed up searches and filtering on these columns.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index('source');
            $table->index('provider');
            $table->index('category_id');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropIndex(['provider']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['published_at']);
        });
    }
};
