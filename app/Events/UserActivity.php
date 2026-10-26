<?php

namespace App\Events;

use App\User;
use Illuminate\Support\Facades\Auth;

class UserActivity
{
    public $paramArray;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($paramArray)
    {
        $paramArray['user_id'] = isset($paramArray['user_id']) ? User::where('U_Code', $paramArray['user_id'])->value('PKey') : Auth::user()->Pkey;
        $paramArray['action_data'] = isset($paramArray['action_data']) ? $paramArray['action_data'] : "";
        $paramArray['comments'] = isset($paramArray['comments']) ? $paramArray['comments'] : "";
        $paramArray['log_from'] = isset($paramArray['log_from']) ? $paramArray['log_from'] : "Web";
        $paramArray['created_at'] = date("Y-m-d H:s:i");
        $this->paramArray = $paramArray;
    }
}
