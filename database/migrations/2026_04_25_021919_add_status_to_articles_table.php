<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('status')->default('published')->after('is_published');
        });

        // Migrate is_published to status
        DB::table('articles')->where('is_published', true)->update(['status' => 'published']);
        DB::table('articles')->where('is_published', false)->update(['status' => 'draft']);

        // Migrate PromoBlog to Article if PromoBlog table exists
        if (Schema::hasTable('promo_blogs')) {
            $promoBlogs = DB::table('promo_blogs')->get();
            foreach ($promoBlogs as $blog) {
                DB::table('articles')->insert([
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'excerpt' => $blog->excerpt,
                    'content' => $blog->content,
                    'ai_summary' => $blog->ai_summary ?? null,
                    'image' => $blog->image,
                    'category' => $blog->category ?: 'Promo',
                    'status' => $blog->status ?: 'published',
                    'is_published' => ($blog->status === 'published'),
                    'views' => $blog->views ?: 0,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
