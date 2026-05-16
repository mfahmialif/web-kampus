<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        DB::table('news')
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->each(function ($news) {
                $base = Str::slug($news->title) ?: 'news-' . $news->id;
                $slug = $base;
                $counter = 2;

                while (DB::table('news')->where('slug', $slug)->where('id', '!=', $news->id)->exists()) {
                    $slug = "{$base}-{$counter}";
                    $counter++;
                }

                DB::table('news')->where('id', $news->id)->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
