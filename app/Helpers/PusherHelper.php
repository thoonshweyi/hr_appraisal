<?php
namespace App\Helpers;

use Pusher\PushNotifications\PushNotifications;

class PusherHelper
{
    public static function sendPushNotification($userId, $title, $message,$appraisal_form_id)
    {
        $beamsClient = new PushNotifications([
            "instanceId" => config('services.beams.instance_id'),
            "secretKey" => config('services.beams.secret_key'),
        ]);

        try {
            $publishResponse = $beamsClient->publishToUsers(
                [$userId], // Target user ID
                [
                    "web" => [
                        "notification" => [
                            "title" => $title,
                            "body" => $message,
                            "deep_link" => route('appraisalforms.show',$appraisal_form_id),
                        ],
                    ],
                ]
            );

            return $publishResponse;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
