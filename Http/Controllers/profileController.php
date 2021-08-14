<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use App\Models\User as Users;
use App\Models\Friends;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Request;

class profileController extends Controller{

    function loadProfileData(){
        $userid=Auth::user()->id;

        $user =Users::where('id',$userid)->get();

        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();

        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();
$Friends=[];
foreach($frienduser as $friend){

    array_push($Friends,$friend->user);

}
foreach($userfriend as $friend){

    array_push($Friends,$friend->userfriend);

    }

    $posts=Posts::where('userid',$userid)->get();
    $userposts=[];
    foreach($posts as $post){
        array_push($userposts,$post);
    }

        //  return MainResource::collection([$user,$Friends,$userposts]);
         return view('viewUserProfile',compact('user','Friends','userposts'));


    }


}


