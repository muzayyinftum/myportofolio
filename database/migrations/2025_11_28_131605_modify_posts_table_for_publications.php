<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop kolom lama
            $table->dropColumn(['content', 'author']);
            
            // Tambah kolom baru untuk publikasi
            $table->enum('type', ['jurnal', 'konferensi', 'hakki'])->after('title');
            $table->json('authors')->after('type'); // Array of authors
            $table->string('journal_name')->nullable()->after('authors');
            $table->string('conference_name')->nullable()->after('journal_name');
            $table->string('hakki_type')->nullable()->after('conference_name'); // Paten, Hak Cipta, dll
            $table->string('publisher')->nullable()->after('hakki_type');
            $table->year('year')->nullable()->after('publisher');
            $table->string('volume')->nullable()->after('year');
            $table->string('issue')->nullable()->after('volume');
            $table->string('pages')->nullable()->after('issue');
            $table->string('doi')->nullable()->after('pages');
            $table->string('isbn')->nullable()->after('doi');
            $table->enum('status', ['draft', 'submitted', 'accepted', 'published'])->default('draft')->after('isbn');
            $table->text('description')->nullable()->after('status'); // Abstract/Description
            $table->string('link')->nullable()->after('description'); // URL publikasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->text('content')->after('title');
            $table->string('author')->after('content');
            
            // Drop kolom baru
            $table->dropColumn([
                'type', 'authors', 'journal_name', 'conference_name', 
                'hakki_type', 'publisher', 'year', 'volume', 'issue', 
                'pages', 'doi', 'isbn', 'status', 'description', 'link'
            ]);
        });
    }
};
