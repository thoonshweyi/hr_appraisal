<?php

namespace App\Http\Controllers;

use Google\Client;
use Illuminate\Http\Request;
use App\Models\FCMSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationController extends Controller
{
    public static function sendNotification(Request $request)
    {
        $user_id = $request->user_id;

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
                        'title' => 'Hello!',
                        'body' => 'Click to see more details.',
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => 'http://127.0.0.1:8000/'
                        ]
                    ]
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
// composer require google/apiclient
