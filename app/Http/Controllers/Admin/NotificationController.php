<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function app_notification()
    {
        return view('admin.notification.send');
    }

    public function send_notification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'message' => 'required'
        ]);
        if($request->device_type == 'all')
        {
            $users = User::get();
        }
        else
        {
            $users = User::where('device_type',$request->device_type)->get();
        }
        foreach($users as $user)
        {
           sendPushNotification($user->device_token,$request->title,$request->message,1,$user->id);
        }
        return response()->json(['status'=>'success']);
    }
}
