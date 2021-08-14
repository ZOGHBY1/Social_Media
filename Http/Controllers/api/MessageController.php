<?php
namespace App\Http\Controllers\api;

use App\Http\Resources\MainResource;
use App\Models\Friends;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Chatify\Http\Models\Message;
use Chatify\Http\Models\Favorite;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class MessageController extends Controller
{


    public function getContacts()
    {
        // get all users that received/sent message from/to [Auth user]
        $userid=auth('api')->user()->id;

        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();

        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();

        $FriendsCount=0;
        $Friends=[];
        foreach($frienduser as $friend){
            $UserData=array(
                "userid"=>$friend->userid,
                "avatar"=>$friend->user->avatar,
                "username"=>$friend->user->firstname
                );
            array_push($Friends,$UserData);
            $FriendsCount++;

        }
        foreach($userfriend as $friend){
            $UserData=array(
                "userid"=>$friend->userFriendid,
                "avatar"=>$friend->userfriend->avatar,
                "username"=>$friend->userfriend->firstname
                );
            array_push($Friends,$UserData);
            $FriendsCount++;
        }



        // send the response
        return MainResource::collection([
            'friends' => $Friends
        ]);

    }


    public function fetch(Request $request)
    {
        // messages variable
        $allMessages = null;
        // here we should to put the user id who you made conversation with him/her or want to make (fetchConversation)
        // fetch messages
        $query = Chatify::fetchMessagesQuery($request['id'])->orderBy('created_at', 'asc');
        $messages = $query->get();

        // if there is a messages
        if ($query->count() > 0) {
            foreach ($messages as $message) {
                $allMessages .= Chatify::messageCard(
                    Chatify::fetchMessage($message->id)
                );
            }
            // dd($allMessages);
            // send the response
            return Response::json([
                'count' => $query->count(),
                'messages' => $allMessages,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $query->count(),
            'messages' => '<p class="message-hint"><span>Say \'hi\' and start messaging</span></p>',
        ]);
    }

    //didnt finish..
    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }



    public function send(Request $request)
    {
        // default variables
        $error_msg = $attachment = $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // if size less than 150MB
            if ($file->getSize() < 150000000) {
                if (in_array($file->getClientOriginalExtension(), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->storeAs("public/" . config('chatify.attachments.folder'), $attachment);
                } else {
                    $error_msg = "File extension not allowed!";
                }
            } else {
                $error_msg = "File size is too long!";
            }
        }

        if (!$error_msg) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            Chatify::newMessage([
                'id' => $messageID,
                'type' => $request['type'],
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => trim(htmlentities($request['message'])),
                'attachment' => ($attachment) ? $attachment . ',' . $attachment_title : null,
            ]);

            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);

            // send to user using pusher
            Chatify::push('private-chatify', 'messaging', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => Chatify::messageCard($messageData, 'default')
            ]);
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error_msg ? 1 : 0,
            'error_msg' => $error_msg,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }




}
