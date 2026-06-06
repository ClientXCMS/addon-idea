<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_idea_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('status')->default('open')->index();
            $table->longText('response')->nullable();
            $table->timestamp('response_at')->nullable();
            $table->foreignId('response_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->unsignedInteger('votes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addon_idea_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 16)->default('2563eb');
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('addon_idea_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('addon_idea_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('addon_idea_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['post_id', 'tag_id']);
        });

        Schema::create('addon_idea_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('addon_idea_posts')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->longText('content');
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->timestamp('edited_at')->nullable();
            $table->foreignId('deleted_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addon_idea_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('addon_idea_posts')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedBigInteger('source_user_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['post_id', 'customer_id']);
        });

        Schema::create('addon_idea_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('addon_idea_posts')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedBigInteger('source_user_id')->nullable()->index();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['post_id', 'customer_id']);
        });

        Schema::create('addon_idea_import_maps', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('fider');
            $table->string('type');
            $table->string('source_id');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->unique(['source', 'type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_idea_import_maps');
        Schema::dropIfExists('addon_idea_subscribers');
        Schema::dropIfExists('addon_idea_votes');
        Schema::dropIfExists('addon_idea_comments');
        Schema::dropIfExists('addon_idea_post_tag');
        Schema::dropIfExists('addon_idea_tags');
        Schema::dropIfExists('addon_idea_posts');
    }
};
