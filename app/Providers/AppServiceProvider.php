<?php

namespace App\Providers;

use App\Models\ApprovalRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $count = 0;
            if ($user?->isSuperAdmin()) {
                $count = ApprovalRequest::pending()->count();
            } elseif ($user?->isAdmin()) {
                $count = ApprovalRequest::where('requested_by', $user->id)
                    ->whereNotNull('reviewed_at')
                    ->whereNull('read_at')
                    ->count();
            }
            $view->with('pendingApprovalCount', $count);
        });
    }
}
