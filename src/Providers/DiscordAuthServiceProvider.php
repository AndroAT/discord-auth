<?php

namespace Azuriom\Plugin\DiscordAuth\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Plugin\DiscordAuth\Models\Discord;
use Illuminate\Support\Facades\Blade;
use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class DiscordAuthServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        // Register Socialite service provider
        $this->app->register(SocialiteServiceProvider::class);

        // Configure Discord provider
        $config = config('plugins.discord-auth.discord');
        
        if (!empty($config['client_id']) && !empty($config['client_secret'])) {
            config(["services.discord" => [
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect' => url('discord-auth/callback')
            ]]);
        }
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('hasDiscordLinked', function () {
            return Discord::where('user_id', auth()->id())->exists();
        });

        Blade::if('hasNotDiscordLinked', function () {
            return !Discord::where('user_id', auth()->id())->exists();
        });

        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerRouteDescriptions();
        $this->registerAdminNavigation();
        $this->registerUserNavigation();

        Permission::registerPermissions([
            'discord-auth.admin' => 'discord-auth::admin.permission',
        ]);
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array
     */
    protected function routeDescriptions()
    {
        return [
            'discord-auth.index' => 'discord-auth::messages.plugin_name',
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array
     */
    protected function adminNavigation()
    {
        return [
            'discord-auth' => [
                'name' => 'discord-auth::admin.nav.title',
                'icon' => 'fab fa-discord',
                'route' => 'discord-auth.admin.settings',
                'permission' => 'discord-auth.admin'
            ],
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     *
     * @return array
     */
    protected function userNavigation()
    {
        return [
            //
        ];
    }
}
