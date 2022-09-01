<?php

use App\Models\Notification;
use App\Models\User;
use App\Models\Setting;

function sendPushNotification($device_token,$noti_title,$noti_body,$badge,$user_id = null,$pledge_id = null,$video_id = null)
{
    $user = User::find($user_id);
    if(isset($user) ? $user->is_notification == 1 : true || $user_id == null)
    {
        $setting = Setting::latest()->first();
        $url = "https://fcm.googleapis.com/fcm/send";
        if(is_array($device_token)){
            $registrationIds = $device_token;
        }else{
            $registrationIds = array($device_token);
        }
        $serverKey = $setting->push_token;
        $title = $noti_title;
        $body = $noti_body;
        $order_data = null;
        if($pledge_id != null || $video_id != null){
            $order_data = ['pledge_id' => $pledge_id, 'video_id' => $video_id];
               
        }
        
        $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' =>$badge);

        $arrayToSend = array('registration_ids' => $registrationIds, 'data' => $order_data, 'notification'=>$notification,'priority'=>'high', "content_available"=> true, "mutable_content"=> true);
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Send the request
        $result = curl_exec($ch);
        if ($result === FALSE) 
        {
            die('FCM Send Error: ' . curl_error($ch));
        }

        curl_close( $ch );
        $notification = New Notification; 
        $notification->user_id = $user_id;
        $notification->video_id = $video_id;
        $notification->pledge_id = $pledge_id;
        $notification->title = $noti_title;
        $notification->message = $noti_body;
        // $notification->payload = $json;
        $notification->result = $result;
        $notification->status = 1;
        $notification->save();
        return $result;
    }
    else
    {
        return 'Notification disabled';
    }
}