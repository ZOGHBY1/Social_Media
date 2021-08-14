<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MainResource;
use App\Models\Employee;
use App\Models\User as Users;
use App\Models\Friends;
use App\Models\Likes;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Hash;

class mainController extends Controller{

    function loadMainData(){
          $userid=Auth::user()->id;
        // $userid=auth('api')->user()->id;

        // $users=Users::orderBy('created_at','DESC')->paginate(10);

        $user =Users::where('id',$userid)->get();
        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();

        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();


        $friendsPosts=[];
        foreach($frienduser as $friend){
            if(count($friend->user->posts)>0){
                foreach($friend->user->posts as $post){
                    array_push($friendsPosts,$post);
                }
            }
        }
        foreach($userfriend as $friend){
            if(count($friend->userfriend->posts)>0){
                foreach($friend->userfriend->posts as $post){
            array_push($friendsPosts,$post);
            }
        }
         }


        $notifications=Friends::where('userFriendid',$userid)
        ->where('requestStatus','pending')
        ->get();
        //  dd($notifications);

        $likes=Likes::where('userid',$userid)->get();
         $userlikes=[];
         foreach($likes as $like){

                 array_push($userlikes,$like);

         }


        // return MainResource::collection(["userinfo"=>$user,"userfriendsPosts"=>$friendsPosts,
        // "usernotifications"=>$notifications,"userlikes"=>$userlikes]);

         return view('viewUserMain',compact('user','friendsPosts','notifications','userlikes'));




    }




}


