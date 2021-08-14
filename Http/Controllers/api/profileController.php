<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use App\Models\Friends;
use App\Models\Posts;

class profileController extends Controller{

    function loadProfileData(){
        $userid=auth('api')->user()->id;
        $posts=Posts::where('userid',$userid)->orderBy('created_at','DESC')->paginate(100);
        $userposts=[];
        foreach($posts as $post){
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
                    "avatar"=>$pcc->user->avatar,
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
                    "avatar"=>$post->user->avatar,
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
            array_push($userposts,$FullPostData);
        }
        return MainResource::collection(["posts"=>$userposts]);
    }


    function friends(){
        $userid=auth('api')->user()->id;
        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();

        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();
        $Friends=[];
        foreach($frienduser as $friend){
            $FD=array(
                "id"=>$friend->user->id,
                "avatar"=>$friend->user->avatar,
                "firstname"=>$friend->user->firstname

                );
            array_push($Friends,$FD);

        }
        foreach($userfriend as $friend){

            $FD=array(
                "id"=>$friend->userfriend->id,
                "avatar"=>$friend->userfriend->avatar,
                "firstname"=>$friend->userfriend->firstname
                );
            array_push($Friends,$FD);

            }
            return MainResource::collection(["friends"=>$Friends]);

    }
}


