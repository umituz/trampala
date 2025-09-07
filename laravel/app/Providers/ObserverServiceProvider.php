<?php

namespace App\Providers;

use App\Models\Country\Country;
use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User\User;
use App\Observers\Auth\UserObserver;
use App\Observers\Country\CountryObserver;
use App\Observers\Currency\CurrencyObserver;
use App\Observers\Language\LanguageObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for registering Eloquent model observers.
 * 
 * This provider registers all model observers to handle model lifecycle events
 * throughout the application. Observers provide hooks for model operations
 * like creating, updating, deleting, etc.
 *
 * @package App\Providers
 */
class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap model observers.
     * 
     * Registers all model observers to listen for model lifecycle events
     * and perform additional operations when models are manipulated.
     *
     * @return void
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Country::observe(CountryObserver::class);
        Currency::observe(CurrencyObserver::class);
        Language::observe(LanguageObserver::class);
    }
}
