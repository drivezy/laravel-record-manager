<?php

namespace Drivezy\LaravelRecordManager\Commands;

use Illuminate\Console\Command;

class ObserverEventCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the observer events that are not processed and push it out';

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
    }
}
