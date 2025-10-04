<?php

namespace App\Services;

class PushNotificationService
{
    public function sendNotification($userId, $message)
    {
        // Here you can integrate with Firebase Cloud Messaging (FCM) or any push notification service
        
        // Sample implementation (modify as per your push notification provider)
        $payload = [
            'to' => $this->getUserDeviceToken($userId),
            'notification' => [
                'title' => 'Reminder Notification',
                'body' => $message,
            ],
            'data' => [
                'user_id' => $userId,
                'message' => $message,
            ],
        ];

        return $this->sendPush($payload);
    }

    private function getUserDeviceToken($userId)
    {
        // Fetch the user's device token from the database (modify as needed)
        // Assuming User model has a device_token column
        $user = \App\Models\User::find($userId);
        return $user ? $user->device_token : null;
    }

    private function sendPush($payload)
    {
        // Example FCM push notification implementation
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY'); // Set this in your .env file

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
