<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeSession;
use App\Policies\ProjectPolicy;
use App\Policies\TagPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TimeSessionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        Project::class => ProjectPolicy::class,
        TimeSession::class => TimeSessionPolicy::class,
        Tag::class => TagPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
