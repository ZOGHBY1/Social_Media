<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User as Users;
use App\Models\Friends;
use App\Models\Posts;
use Illuminate\Support\Facades\Hash;

class userController extends Controller{
    function banCheck(){
        $userid=auth('api')->user()->id;
        $checkban=Users::where('id',$userid)->get();
        foreach($checkban as $CBan){
            if($CBan->ban=="0"){
                return false;



            }else{
                return true;


            }
        }
    }

    function setCategory(Request $req){
        $userid=auth('api')->user()->id;
        $user=Users::find($userid);
        $user->category=$req->category;
        $user->save();
    }

    function editInfo(){
        //if(banCheck()){}
        $userid=auth('api')->user()->id;
        $data=Users::where('id',$userid)->get();
        return MainResource::collection($data);

    }

    function editUserInfo(Request $req){
        $userid=auth('api')->user()->id;
        $use=Users::find($userid);
        $use->firstname=$req->fname;
        $use->lastname=$req->lname;
        $use->email=$req->email;
        $use->gender=$req->gender;
        $use->region=$req->region;
        $use->birthyear=$req->byear;
        $use->birthday=$req->bday;
        $use->birthmonth=$req->bmonth;
        $use->phonenumber=$req->phonenumber;
        $use->category=$req->category;
        $use->profilestatus=$req->profilestatus;
        $use->save();
        return MainResource::collection($use);
    }


    function deleteAccount(){
        $userid=auth('api')->user()->id;
        $deleteuser=Users::where('userid',$userid);
        $deleteuser->delete();
        return json_encode(true);
    }


    function ResetPass(){
        $userid=auth('api')->user()->id;
        $data=Users::where('id',$userid)->get();
        return MainResource::collection($data);
    }

    function ResetUserPass(Request $req){
        $userid=auth('api')->user()->id;
        $use=Users::find($userid);
        $use->password=Hash::make($req->password);
        $use->save();
        return json_encode(true);
    }

    function userData(){
        $userid=auth('api')->user()->id;
        $user=Users::where('id',$userid)->get();
        return MainResource::collection($user);
    }





    function addFriend($userFriendid){
        $userid=auth('api')->user()->id;
        $checkprofileStatus=Users::where('id',$userFriendid)->get();

        foreach($checkprofileStatus as $cps){
            if($cps->profilestatus=="private"){

                    Friends::create([
                    'userid'=>$userid ,
                    'userFriendid'=>$cps->id ,
                    'requestStatus'=>'pending'
                    ]) ;

                return
                json_encode(true);

            }else{
                     Friends::create([
                    'userid'=>$userid ,
                    'userFriendid'=>$cps->id ,
                    'requestStatus'=>'friends'
                    ]) ;
                //    return redirect()->back();
                return json_encode(true);

            }
        }
    }


    function unRequest($userFriendid){
        $userid=auth('api')->user()->id;
        $friendship=Friends::where('userid',$userid)->where('userFriendid',$userFriendid);
        $friendship->delete();
        // return redirect()->back();
        return json_encode(true);

    }


    function unFriend($userFriendid){
        $userid=auth('api')->user()->id;
        $friendship=Friends::where('userid',$userid)
        ->where('userFriendid',$userFriendid)
        ->orwhere('userid',$userFriendid)
        ->where('userFriendid',$userid);
        $friendship->delete();
        // return redirect()->back();
        return json_encode(true);
    }

    function acceptFriend($userFriendid){
        $userid=auth('api')->user()->id;
        DB::table('friends')
        ->where('userid',$userFriendid)
        ->where('userFriendid',$userid)->update(['requestStatus'=>'friends']);
       // dd($friendship);
        // return redirect()->back();
        return json_encode(true);
    }

    function ignoreFriend($userFriendid){
        $userid=auth('api')->user()->id;
        $friendship=Friends::where('userid',$userFriendid)->where('userFriendid',$userid);
        $friendship->delete();
        // return redirect()->back();
        return json_encode(true);
    }







    function seeProfile($userFriendid){
        $userid=auth('api')->user()->id;
        $user=Users::where('id',$userFriendid)->get();
        $profileData=[];
        // $profileposts=[];
        $friendstatus="strange";
        $userposts=Posts::where('userid',$userFriendid)
                ->orderBy('created_at','DESC')->paginate(100);
                $numOfPosts=0;

                $frienduser=Friends::where('userFriendid',$userFriendid)
                ->where('requestStatus','friends')
                ->get();
                $userfriend=Friends::where('userid',$userFriendid)
                ->where('requestStatus','friends')
                ->get();
                $numOfFriends=0;
                foreach($frienduser as $friend){
                    $numOfFriends++;
                }
                foreach($userfriend as $friend){
                    $numOfFriends++;
                }

        foreach($userposts as $post){
            $numOfPosts++;
        //     $isLiked=false;
        //     foreach($post->likes as $postlikes){
        //             if ($postlikes->userid==$userid){$isLiked=true;}else{$isLiked=false;}
        //     }
        //     $postComments=Posts::where('postid',$post->postid)->get();
        //     $comments=[];
        //     foreach($postComments as $pc){
        //         foreach($pc->comment as $pcc){
        //             $FullCommentData=array(
        //                 "postid"=>$pcc->postid,
        //                 "userid"=>$pcc->userid,
        //                 "username"=>$pcc->user->firstname,
        //                 "comment"=>$pcc->comment
        //             );
        //             array_push($comments,$FullCommentData);
        //         }
        //     }

        //     $FullPostData=array(
        //                     "postid"=>$post->postid,
        //                     "userid"=>$post->userid,
        //                     "username"=>$post->user->firstname,
        //                     "posttitle"=>$post->posttitle,
        //                     "post"=>$post->post,
        //                     "isLiked"=>$isLiked,
        //                     "comments"=>$comments,
        //                     "likesCounter"=>$post->likesCounter,
        //                     "commentsCounter"=>$post->commentsCounter,
        //                     "category"=>$post->category,
        //                     "created_at"=>$post->created_at
        //                     );
        //             array_push($profileposts,$FullPostData);

        }

        $frienduser=Friends::where('userFriendid',$userid)
        ->where('userid',$userFriendid)->get();
        $userfriend=Friends::where('userid',$userid)
        ->where('userFriendid',$userFriendid)->get();

        if(count($frienduser)>0){
            if($userFriendid==$userid){
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
            if($userFriendid==$userid){
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

        foreach($user as $use){
            $FullData=array(
                "userid"=>$use->id,
                "avatar"=>$use->avatar,
                "firstname"=>$use->firstname,
                "lastname"=>$use->lastname,
                "numOfPosts"=>$numOfPosts,
                "numOfFriends"=>$numOfFriends,
                // "posts"=>$profileposts,
                "friendstatus"=>$friendstatus,
                "profilestatus"=>$use->profilestatus
            );
            array_push($profileData,$FullData);
        }

            return MainResource::collection(["profiledata"=>$profileData]);
    }



    function seeprofilePosts($userFriendid){

        $userid=auth('api')->user()->id;
            $userposts=Posts::where('userid',$userFriendid)
            ->orderBy('created_at','DESC')->paginate(100);
            $profileposts=[];
            foreach($userposts as $post){
                $isLiked=false;
                foreach($post->likes as $postlikes){
                        if ($postlikes->userid==$userid){$isLiked=true;}else{$isLiked=false;}
                }
                $postComments=Posts::where('postid',$post->postid)->get();
                $comments=[];
                foreach($postComments as $pc){
                    foreach($pc->comment as $pcc){
                        $FullCommentData=array(
                            "commenttid"=>$pcc->commentid,
                            "postid"=>$pcc->postid,
                            "userid"=>$pcc->userid,
                            "username"=>$pcc->user->firstname,
                            "comment"=>$pcc->comment
                        );
                        array_push($comments,$FullCommentData);
                    }
                }

                $FullPostData=array(
                                "postid"=>$post->postid,
                                "userid"=>$post->userid,
                                "username"=>$post->user->firstname,
                                "posttitle"=>$post->posttitle,
                                "post"=>$post->post,
                                "isLiked"=>$isLiked,
                                "comments"=>$comments,
                                "likesCounter"=>$post->likesCounter,
                                "commentsCounter"=>$post->commentsCounter,
                                "category"=>$post->category,
                                "created_at"=>$post->created_at
                                );
                        array_push($profileposts,$FullPostData);

            }

            return MainResource::collection(["profileposts"=>$profileposts]);

    }

}
















