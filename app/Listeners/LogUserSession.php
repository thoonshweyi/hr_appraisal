<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserSession;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;
class LogUserSession
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
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $ip = Request::ip();  // User IP
        $position = Location::get($ip);

        $agent = new Agent();

        $deviceType = $agent->device();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $isDesktop = $agent->isDesktop() ? 'Desktop' : 'Mobile/Tablet';


        UserSession::updateOrCreate(
            ['session_id' => Session::getId()],
            [
                'user_id'      => $event->user->id,
                'ip_address'   => $ip,
                'user_agent'   => substr(Request::header('User-Agent'), 0, 500),
                'last_activity'=> now(),
                'country'      => $position ? $position->countryName : null,
                'region'       => $position ? $position->regionName : null,
                'city'         => $position ? $position->cityName : null,
                'device_type'   => $isDesktop,
                'device_name'   => $deviceType ?: 'Unknown Device',
                'browser'       => $browser ?: 'Unknown Browser',
                'platform'      => $platform ?: 'Unknown Platform',
            ]
        );
    }
}
