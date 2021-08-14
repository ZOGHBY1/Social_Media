<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User as Users;
use App\Models\Friends;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;

class userController extends Controller{

    function editInfo(Request $req){
        $userid=Auth::user()->id;
    }

    function deleteAccount(){
        $userid=Auth::user()->id;
    }
    function addFriend($userFriendid){
        $userid=Auth::user()->id;
        $checkprofileStatus=Users::where('id',$userFriendid)->get();

        foreach($checkprofileStatus as $cps){
            if($cps->profilestatus=="private"){

                    Friends::create([
                    'userid'=>$userid ,
                    'userFriendid'=>$cps->id ,
                    'requestStatus'=>'pending'
                    ]) ;

                   return redirect()->back();
            }else{
                     Friends::create([
                    'userid'=>$userid ,
                    'userFriendid'=>$cps->id ,
                    'requestStatus'=>'friends'
                    ]) ;
                   return redirect()->back();
                //dd(Auth()->user());

            }
        }
    }


    function unRequest($userFriendid){
        $userid=Auth::user()->id;
        $friendship=Friends::where('userid',$userid)->where('userFriendid',$userFriendid);
        $friendship->delete();
        return redirect()->back();

    }


    function unFriend($userFriendid){
        $userid=Auth::user()->id;
        $friendship=Friends::where('userid',$userid)
        ->where('userFriendid',$userFriendid)
        ->orwhere('userid',$userFriendid)
        ->where('userFriendid',$userid);
        $friendship->delete();
        return redirect()->back();
    }

    function acceptFriend($userFriendid){
        $userid=Auth::user()->id;
        DB::table('friends')
        ->where('userid',$userFriendid)
        ->where('userFriendid',$userid)->update(['requestStatus'=>'friends']);
       // dd($friendship);
        return redirect()->back();
    }

    function ignoreFriend($userFriendid){
        $userid=Auth::user()->id;
        $friendship=Friends::where('userid',$userFriendid)->where('userFriendid',$userid);
        $friendship->delete();
        return redirect()->back();
    }

    function seeProfile($userFriendid){
        $userid=Auth::user()->id;
        $user =Users::where('id',$userid)->get();
        $userFriendInfo=Users::where('id',$userFriendid)->get();
        $frienduser=Friends::where('userid',$userid)
        ->where('userFriendid',$userFriendid)
        ->get();
        $userfriend=Friends::where('userFriendid',$userid)
        ->where('userid',$userFriendid)
        ->get();
         $userposts=[];
         $poststatus=true;
        if(count($frienduser)>0){
            foreach($frienduser as $fu){
                if($fu->requestStatus=="friends"){
                    foreach($userFriendInfo as $ufi){
                            if(count($ufi->posts)>0){
                                foreach($ufi->posts as $post){
                                    array_push($userposts,$post);
                                }
                                //dd("this account is friend and have posts..",$ufi->profilestatus);
                            }else{
                                $poststatus=false;
                                //dd("this account is friend and have no posts..",$ufi->profilestatus);
                            }
                            }
                }elseif($fu->user->profileStatus=="public"){
                    foreach($userFriendInfo as $ufi){
                        if(count($ufi->posts)>0){
                            foreach($ufi->posts as $post){
                                array_push($userposts,$post);
                            }
                            //dd("this account is public and have posts..Friends table",$ufi->profilestatus);
                        }else{
                            $poststatus=false;
                            //dd("this account is public and have no posts..Friends table",$ufi->profilestatus);
                        }

                    }
                }

            }
        }elseif(count($userfriend)>0){
                foreach($userfriend as $fu){
                    if($fu->requestStatus=="friends"){
                        foreach($userFriendInfo as $ufi){
                        if(count($ufi->posts)>0){
                            foreach($ufi->posts as $post){
                            array_push($userposts,$post);
                            }
                           // dd("this account is friend and have posts..",$ufi->profilestatus);
                        }else{
                            $poststatus=false;
                           // dd("this account is friend and have no posts..",$ufi->profilestatus);
                        }
                        }
                    }elseif($fu->user->profileStatus=="public"){
                        foreach($userFriendInfo as $ufi){
                        if(count($ufi->posts)>0){
                            foreach($ufi->posts as $post){
                            array_push($userposts,$post);
                            }
                           // dd("this account is public and have posts..Friends table",$ufi->profilestatus);
                    }else{
                        $poststatus=false;
                       // dd("this account is public and have no posts..Friends table",$ufi->profilestatus);
                    }
                    }
                }

            }

        }else{
            foreach($userFriendInfo as $ufi){
                if($ufi->profilestatus=="public"){
                    foreach($userFriendInfo as $ufi){
                        if(count($ufi->posts)>0){
                            foreach($ufi->posts as $post){
                            array_push($userposts,$post);
                           // dd("this account is public and have posts..not infriends table",$ufi->profilestatus);
                            }
                        }else{
                            $poststatus=false;
                            //dd("this account is public and have no posts..not in friends table",$ufi->profilestatus);
                        }
                    }
                }
            }
        }
        $friendStatus=[];
        if(count($frienduser)>0){
            foreach($frienduser as $friend){
            array_push($friendStatus,$friend);
            }
        }else{
            foreach($userfriend as $friend){
                array_push($friendStatus,$friend);
            }
        }

        return view('seeProfile',compact('user','userFriendInfo','userposts','friendStatus','poststatus'));
    }





}


