<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;
use App\Models\User as Users;
use App\Models\Friends;
use Illuminate\Support\Facades\Auth;

class searchController extends Controller{


    function Result(Request $req){
        $userid=Auth::user()->id;
        $user =Users::where('id',$userid)->get();
         $search=Users::where('firstname',$req->searchvalue)
         ->orwhere('lastname',$req->searchvalue)
         ->orwhere('email',$req->searchvalue)
         ->orwhere('phoneNumber',$req->searchvalue)
         ->get();

         $searchResult=[];
         foreach($search as $s){
             array_push($searchResult,$s);
         }

         $friendStatus=[];

         foreach($searchResult as $sR){
            $frienduser=Friends::where('userFriendid',$userid)
            ->where('userid',$sR->id)
            ->get();
            $userfriend=Friends::where('userid',$userid)
            ->where('userFriendid',$sR->id)
            ->get();
            if(count($frienduser)>0){
                foreach($frienduser as $friend){
                  array_push($friendStatus,$friend);
                }
            }else{
                foreach($userfriend as $friend){
                    array_push($friendStatus,$friend);
                  }
            }
        }

        // return MainResource::collection(["userinfo"=>$user,"searchResult"=>$searchResult,"friendStatus"=>$friendStatus]);
         return view('searchResult',compact('user','searchResult','friendStatus'));
    }

}
