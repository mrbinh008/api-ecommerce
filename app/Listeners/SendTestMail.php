<?php

namespace App\Listeners;

use App\Events\TestMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTestMail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }


    /**
     * Handle the event.
     */
    public function handle(TestMail $event): void
    {
        \Mail::to($event->email)->queue(new \App\Mail\MTest($event->email, $event->name));
    }
}
