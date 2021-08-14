<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;
use App\Models\User as Users;
use App\Models\Friends;
use Illuminate\Support\Facades\Auth;

class searchController extends Controller{


    function Result(Request $req){
        $userid=auth('api')->user()->id;
        $search=Users::where('firstname','LIKE', "%{$req->searchvalue}%")
        ->orwhere('lastname','LIKE', "%{$req->searchvalue}%")
        ->orwhere('email','LIKE', "%{$req->searchvalue}%")
        ->orwhere('phoneNumber','LIKE', "%{$req->searchvalue}%")
        ->get();
        $searchResult=[];

        foreach($search as $s){
            $friendstatus="strange";
            $frienduser=Friends::where('userFriendid',$userid)
            ->where('userid',$s->id)->get();
            $userfriend=Friends::where('userid',$userid)
            ->where('userFriendid',$s->id)->get();

            if(count($frienduser)>0){
                if($s->id==$userid){
                    $friendstatus="mine";
                }else{
                    foreach($frienduser as $friend){
                        if($friend->requestStatus=="friends"){
                            $friendstatus="friends";
                        }else{
                            $friendstatus="pending";
                        }
                    }
                }


            }else{
                if($s->id==$userid){
                    $friendstatus="mine";
                }else{
                    foreach($userfriend as $friend){
                        if($friend->requestStatus=="friends"){
                            $friendstatus="friends";
                        }else{
                            $friendstatus="requested";
                        }
                    }
                }

            }

            $SR=array(
            "id"=>$s->id,
            "avatar"=>$s->avatar,
            "username"=>$s->firstname." ".$s->lastname,
            "friendstatus"=>$friendstatus
            );
            array_push($searchResult,$SR);
         }

        return MainResource::collection(["searchResult"=>$searchResult]);
    }

}
