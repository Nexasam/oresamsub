<?php

namespace App\Listeners;

use App\Events\Registered;
use IlluminateAuthEventsRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Services\VirtualAccountService;

class CreateVirtualAccountForNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;
        $dataa['user'] = $user;
        (new VirtualAccountService())->generate_accounts($dataa);
    }
}
