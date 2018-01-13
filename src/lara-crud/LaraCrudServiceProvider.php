<?php


namespace LaraCrud;

use DbReader\Database;
use Illuminate\Support\ServiceProvider;
use LaraCrud\Console\Controller;
use LaraCrud\Console\Migration;
use LaraCrud\Console\Model;
use LaraCrud\Console\Mvc;
use LaraCrud\Console\Package;
use LaraCrud\Console\Policy;
use LaraCrud\Console\Request;
use LaraCrud\Console\Route;
use LaraCrud\Console\Test;
use LaraCrud\Console\Transformer;
use LaraCrud\Console\View;

/**
 * Description of LaraCrudServiceProvider
 *
 * @author Tuhin
 */
class LaraCrudServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * List of command which will be registered.
     * @var array
     */
    protected $commands = [
        Model::class,
        Request::class,
        Controller::class,
        Route::class,
        Migration::class,
        View::class,
        Mvc::class,
        Policy::class,
        Transformer::class,
        Test::class,
        Package::class
    ];

    /**
     * Run on application loading
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laracrud.php' => config_path('laracrud.php')
        ], 'laracrud-config');

        // Publish Templates to view/vendor folder so user can customize this own templates
        $this->publishes([
            __DIR__ . '/../../resources/templates' => resource_path('views/vendor/laracrud/templates')
        ], 'laracrud-template');

    }

    /**
     * Run after all boot method completed
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laracrud.php', 'laracrud'
        );
        if ($this->app->runningInConsole()) {
            //DbReader\Database settings
            Database::settings([
                'pdo' => app('db')->connection()->getPdo(),
                'manualRelations' => config('laracrud.model.relations', []),
                'ignore' => config('laracrud.view.ignore', []),
                'protectedColumns' => config('laracrud.model.protectedColumns', []),
                'files' => config('laracrud.image.columns', [])
            ]);
            $this->commands($this->commands);
        }

    }

    /**
     * To register laracrud as first level command. E.g. laracrud:model
     *
     * @return array
     */
    public function provides()
    {
        return ['laracrud'];
    }
}