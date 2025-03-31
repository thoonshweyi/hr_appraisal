<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PusherHelper;
use Pusher\PushNotifications\PushNotifications;

class PushNotificationController extends Controller
{
    // public function sendNotification()
    // {
    //     $beamsClient = new PushNotifications([
    //         "instanceId" => config('services.beams.instance_id'),
    //         "secretKey" => config('services.beams.secret_key'),
    //     ]);

    //     $publishResponse = $beamsClient->publishToInterests(
    //         ["general"], // Interest group
    //         [
    //             "web" => [
    //                 "notification" => [
    //                     "title" => "New Notification",
    //                     "body" => "This is a push notification from Pusher Beams!",
    //                     "deep_link" => "https://yourwebsite.com",
    //                 ],
    //             ],
    //         ]
    //     );

    //     return response()->json($publishResponse);
    // }

    public function sendNotification(Request $request)
    {
        // $request->validate([
        //     'user_id' => 'required|string',
        //     'title'   => 'required|string',
        //     'message' => 'required|string',
        // ]);

        // dd($request);
        $response = PusherHelper::sendPushNotification(
            $request->user_id,
            $request->title,
            $request->message
        );

        return response()->json(['status' => 'success', 'response' => $response]);
    }
}
