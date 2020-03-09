<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Jobs\ObserverEventManagerJob;
use Drivezy\LaravelRecordManager\Models\ObserverEvent;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;
use Drivezy\LaravelUtility\Library\QueueManager;
use Illuminate\Support\Facades\Cache;

class ObserverQueueManager extends QueueManager
{
    private $maxItemsToPush = 0;

    /**
     * ObserverQueueManager constructor.
     */
    public function __construct ()
    {
        $this->identifier = 'observer.queue.push';
        parent::__construct();
    }

    /**
     * This would poll the event queue table and would process it.
     */
    public function processQueue ()
    {
        $lastRestartTime = $this->getLastRestartTime();
        while ( true ) {
            $this->needsRestart($lastRestartTime);

            $this->maxItemsToPush = $this->getMaximumNumberOfEntriesToPush();

            $chunkLimit = LaravelUtility::getProperty('queue.observer.chunk', 200);
            $chunkLimit = $chunkLimit < $this->maxItemsToPush ? $chunkLimit : $this->maxItemsToPush;

            ObserverEvent::active()->chunk($chunkLimit, function ($objects) use ($lastRestartTime, $chunkLimit) {
                //check if the code has been updated and process needs restarting
                $this->needsRestart($lastRestartTime);

                foreach ( $objects as $object ) {
                    if ( $this->maxItemsToPush <= 0 ) return false;

                    if ( $object->processed_at ) continue;

                    dispatch(new ObserverEventManagerJob($object->id));

                    $object->processed_at = DateUtil::getDateTime();
                    $object->save();

                    --$this->maxItemsToPush;
                }
            });
            //check if the program has to sleep or not
            $this->rest();
            Cache::forever($this->identifier, false);

        }
    }
}
