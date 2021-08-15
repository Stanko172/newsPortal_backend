<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index(Request $request){
        switch($request->filter){
            case 'all':
                return Auth::user()->notifications;
                break;
            
            case 'unread':
                return Auth::user()->unreadNotifications;
                break;
            
            case 'read':
                $notifications = Auth::user()->notifications->where('read_at', '!=', null)->flatten();
                return $notifications;
                break;
        }
        
    }

    public function edit(Request $request){
        $user = Auth::user();
        foreach($user->notifications as $notification){
            if(in_array($notification->id, $request->id_array)){
                if($notification->read_at == null){
                    $notification->markAsRead();
                }else{
                    $notification->update(['read_at' => null]);
                }
            }
        }

        return response()->json(['succes' => 'Status changed.', 200]);
    }

    public function delete(Request $request){
        foreach(Auth::user()->notifications as $notification){
            if($notification->id == $request->id){
                if($notification->delete()){
                    return response()->json(['success' => "Notification deleted"], 200);
                }else{
                    return response()->json(['error' => "Error while deleting notification"], 500);
                }
            }
        }
    }

    public function unread_num(){
        $unread_num = count(Auth::user()->unreadNotifications);
        return response()->json(['unread_num' => $unread_num]);
    }
}
