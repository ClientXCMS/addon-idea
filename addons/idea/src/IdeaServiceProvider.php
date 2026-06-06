<?php

namespace App\Addons\Idea;

use App\Addons\Idea\Controllers\Admin\IdeaCommentController;
use App\Addons\Idea\Controllers\Admin\IdeaImportController;
use App\Addons\Idea\Controllers\Admin\IdeaPostController;
use App\Addons\Idea\Controllers\Admin\IdeaTagController;
use App\Extensions\BaseAddonServiceProvider;
use App\Models\Admin\Permission;
use Database\Seeders\IdeaSeeder;

class IdeaServiceProvider extends BaseAddonServiceProvider
{
    protected string $uuid = 'idea';


    public function boot()
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->addSeeder(IdeaSeeder::class);

        \Route::middleware(['web', 'admin'])
            ->prefix(admin_prefix('ideas'))
            ->name('admin.ideas.')
            ->group(function () {
                require addon_path($this->uuid, 'routes/admin.php');
            });

        \Route::middleware(['web'])
            ->prefix('ideas')
            ->name('ideas.')
            ->group(function () {
                require addon_path($this->uuid, 'routes/web.php');
            });

        $settings = $this->app->make('settings');
        $settings->addCard('ideas', 'idea::lang.admin.settings.title', 'idea::lang.admin.settings.description', 6);
        $settings->addCardItem('ideas', 'posts', 'idea::lang.admin.posts.title', 'idea::lang.admin.posts.description', 'bi bi-lightbulb', [IdeaPostController::class, 'index'], Permission::MANAGE_EXTENSIONS);
        $settings->addCardItem('ideas', 'comments', 'idea::lang.admin.comments.title', 'idea::lang.admin.comments.description', 'bi bi-chat-left-text', [IdeaCommentController::class, 'index'], Permission::MANAGE_EXTENSIONS);
        $settings->addCardItem('ideas', 'tags', 'idea::lang.admin.tags.title', 'idea::lang.admin.tags.description', 'bi bi-tags', [IdeaTagController::class, 'index'], Permission::MANAGE_EXTENSIONS);
        $settings->addCardItem('ideas', 'import', 'idea::lang.admin.import.title', 'idea::lang.admin.import.description', 'bi bi-upload', [IdeaImportController::class, 'index'], Permission::MANAGE_EXTENSIONS);
    }
}
