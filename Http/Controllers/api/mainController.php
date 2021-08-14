<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use App\Models\Friends;
use App\Models\Posts;

class mainController extends Controller{

    function loadMainData(){

        $userid=auth('api')->user()->id;
        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();
        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();
        $friendsPosts=[];
        foreach($frienduser as $friend){
            if(count($friend->user->posts)>0){
                $popost=Posts::where('userid',$friend->user->id)->orderBy('created_at','DESC')->paginate(100);
                foreach($popost as $post){
                    $isLiked=false;
                    foreach($post->likes as $postlikes){
                        if ($postlikes->userid==$userid){$isLiked=true;}else{$isLiked=false;}
                    }
                    $postComments=Posts::where('postid',$post->postid)
                    ->orderBy('created_at','DESC')->paginate(100);
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
                             array_push($friendsPosts,$FullPostData);

                }
            }
        }
        foreach($userfriend as $friend){
                if(count($friend->userfriend->posts)>0){
                    $popost=Posts::where('userid',$friend->userfriend->id)->orderBy('created_at','DESC')->paginate(100);
                    foreach($popost as $post){
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
                                 array_push($friendsPosts,$FullPostData);
                }
            }
         }





        return MainResource::collection(["userfriendsPosts"=>$friendsPosts]);

    }


    function notifications(){
        $userid=auth('api')->user()->id;
        $notifiy=Friends::where('userFriendid',$userid)
        ->where('requestStatus','pending')
        ->get();
        $notifications=[];
        foreach($notifiy as $noti){
            $userdata=array(
                "userid"=>$noti->userid,
                "avatar"=>$noti->user->avatar,
                "username"=>$noti->user->firstname." ".$noti->user->lastname
                );

                array_push($notifications,$userdata);

        }
        return MainResource::collection(["friendrequest"=>$notifications]);
    }

}


