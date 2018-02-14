<?php

namespace Dinkbit\ConektaCashier;

use yii\base\Component;

class CashierServiceProvider extends Component
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     * TODO UPDATE THIS TO YII
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Dinkbit\ConektaCashier\BillableRepositoryInterface', function () {
            return new EloquentBillableRepository();
        });

        $this->app->singleton('command.conekta.cashier.table', function ($app) {
            return new CashierTableCommand();
        });

        $this->commands('command.conekta.cashier.table');
    }
}
