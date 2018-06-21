<?php

namespace Drivezy\LaravelRecordManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Class CodeGeneratorCommand
 * @package Drivezy\LaravelRecordManager\Console
 */
class CodeGeneratorCommand extends Command {

    /**
     * @var
     */
    protected $table;
    /**
     * @var
     */
    protected $namespace;
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $read;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dev code for development purpose';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct () {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle () {
        $this->table = $this->ask('What is the name of the table');
        $this->namespace = $this->ask('What is the namespace of deployment');
        $this->name = $this->ask('What is the model name');
        $this->read = $this->choice('Is the controller readonly', ['y', 'n'], 1);

        $this->read = $this->read == 'y' ? 'Read' : '';

        $this->generateFile();
    }

    /**
     * Generate files required for the development in one shot
     */
    public function generateFile () {
        $this->generateMigration();
        $this->verifyNamespaceValidity();
        $this->generateResourceFiles();
    }

    /**
     * Generate migration file and then update it according to the stub file
     */
    public function generateMigration () {
        Artisan::call('make:migration', [
            'name'     => 'create_' . $this->table . '_table',
            '--create' => $this->table,
        ]);

        $migrationFile = database_path() . '/migrations/' . scandir(database_path() . '/migrations', SCANDIR_SORT_DESCENDING)[0];
        $contents = file_get_contents($migrationFile);

        $template = file_get_contents(__DIR__ . '/../Templates/MigrationTemplate.stub');
        unlink($migrationFile);

        $contents = str_replace('$table->increments(\'id\');', $template, $contents);
        file_put_contents($migrationFile, $contents);

        $this->info('Created Migration File : ' . str_replace(database_path(), '', $migrationFile));
    }

    /**
     * Verify if the directory exists for the namespace
     * If not then create one
     */
    public function verifyNamespaceValidity () {
        //check if models directory is created in app
        if ( !is_dir(app_path() . '/Models') ) mkdir(app_path() . '/Models');

        //check if observers are created in app
        if ( !is_dir(app_path() . '/Observers') ) mkdir(app_path() . '/Observers');

        //check for model namespace directory
        if ( !is_dir(app_path() . '/Models/' . $this->namespace) ) {
            mkdir(app_path() . '/Models/' . $this->namespace);
            $this->info('Created Directory : ' . 'Models/' . $this->namespace);
        }

        //check for observer namespace directory
        if ( !is_dir(app_path() . '/Observers/' . $this->namespace) ) {
            mkdir(app_path() . '/Observers/' . $this->namespace);
            $this->info('Created Directory : ' . 'Observers/' . $this->namespace);
        }

        //check for controller namespace directory
        if ( !is_dir(app_path() . '/Http/Controllers/' . $this->namespace) ) {
            mkdir(app_path() . '/Http/Controllers/' . $this->namespace);
            $this->info('Created Directory : ' . 'Http/Controllers/' . $this->namespace);
        }
    }

    /**
     * Create observer, model and then controller
     */
    public function generateResourceFiles () {
        $content = $this->replaceContents(file_get_contents(__DIR__ . '/../Templates/ObserverTemplate.stub'));
        $file = app_path() . '/Observers/' . $this->namespace . '/' . $this->name . 'Observer.php';
        file_put_contents($file, $content);
        $this->info('Created Observer File : ' . str_replace(app_path(), '', $file));

        $content = $this->replaceContents(file_get_contents(__DIR__ . '/../Templates/ModelTemplate.stub'));
        $file = app_path() . '/Models/' . $this->namespace . '/' . $this->name . '.php';
        file_put_contents($file, $content);
        $this->info('Created Model File : ' . str_replace(app_path(), '', $file));

        $content = $this->replaceContents(file_get_contents(__DIR__ . '/../Templates/ControllerTemplate.stub'));
        $file = app_path() . '/Http/Controllers/' . $this->namespace . '/' . $this->name . 'Controller.php';
        file_put_contents($file, $content);
        $this->info('Created Controller File : ' . str_replace(app_path(), '', $file));
    }

    /**
     * Replace the inline variables with the user input ones
     * @param $content
     * @return mixed
     */
    public function replaceContents ($content) {
        $content = str_replace('{{app}}', config('utility.app_namespace'), $content);
        $content = str_replace('{{namespace}}', $this->namespace, $content);
        $content = str_replace('{{name}}', $this->name, $content);
        $content = str_replace('{{table}}', $this->table, $content);
        $content = str_replace('{{read}}', $this->read, $content);

        return $content;
    }
}