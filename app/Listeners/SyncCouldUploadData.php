<?php

namespace App\Listeners;

use App\Events\CouldUploadDataRecived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncCouldUploadData implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CouldUploadDataRecived $event): void
    {
        //
        // Extract the movie data from the event



    }
}
