<?php
namespace App\Helpers;

use Google\Client;
use Illuminate\Http\Request;
use App\Models\FCMSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class FCMHelper
{
    public static function sendFCMNotification($user_id, $title, $message,$appraisal_form_id)
    {
        $serviceAccountPath = storage_path('firebase/service-account.json');
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        $projectId = 'hr-appraisal';
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $fcmTokens = FCMSubscription::where('user_id', $user_id)->pluck('fcm_token')->toArray();

        $results = [];

        foreach ($fcmTokens as $fcmToken) {
            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'data' => [
                        'click_action' => 'https://your-website.com/some-path',
                        'link' => 'https://your-website.com/some-path',
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => 'https://your-website.com/some-path',
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)->post($url, $message);
            $results[] = [
                'token' => $fcmToken,
                'status' => $response->status(),
                'response' => $response->json(),
                'status' => $response->status(),
            ];
        }

        Log::info($results);
        return response()->json([
            'results' => $results,
        ]);
    }
}
