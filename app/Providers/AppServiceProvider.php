<?php

namespace App\Providers;

use App\Interfaces\PeerToPeerRepositoryInterface;
use App\Repositories\PeerToPeerRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PeerToPeerRepositoryInterface::class, PeerToPeerRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*',function($view){
            $view->with("authuser",Auth::user());
        });
        Paginator::useBootstrap();
    }
}
