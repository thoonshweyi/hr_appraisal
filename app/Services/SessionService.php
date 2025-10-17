<?php

namespace App\Services;

use App\Models\UserSession;
use Illuminate\Support\Facades\Session;


class SessionService{
    protected string $currentSessionId;

    public function __construct()
    {
        $this->currentSessionId = Session::getId(); 
    }

    public function getCurrentDevice(){
        $currentDevice = UserSession::where('user_id', auth()->id())
                        ->where('session_id', $this->currentSessionId)
                        ->first();

        return $currentDevice;
    }

    public function getOtherSessions(){
        $otherSessions = UserSession::where('user_id', auth()->id())
        ->where('session_id', '!=', $this->currentSessionId)
        ->get();

        return $otherSessions;
    }
}

?>
