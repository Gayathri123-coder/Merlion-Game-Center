<?php

namespace App\Listeners;

use App\UserLog;

class UserActivitySave
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($paramArray)
    {
        $UserLog = new UserLog();
        $UserLog->fill($paramArray->paramArray);
        $UserLog->save();
    }
}
