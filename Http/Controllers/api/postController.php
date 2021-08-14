<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;
use App\Models\Likes;
use App\Models\Posts;
use App\Models\User as Users;
use App\Models\Comments;
use App\Models\Reports;

class postController extends Controller{

        function Discovery(){
            $userid=auth('api')->user()->id;
            $user=Users::where('id',$userid)->get();
            $usercategory=null;
            foreach($user as $zuser){$usercategory=$zuser->category;}
            $disposts=[];
            foreach($user as $u){
                $users=Users::where('profilestatus','public')->get();

                foreach($users as $use){

                    if($usercategory=="All"){
                        $posts=Posts::where('userid',$use->id)
                        ->orderBy('created_at','DESC')->paginate(100);
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
                                                            "avatar"=>$use->avatar,
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
                                                    array_push($disposts,$FullPostData);






                        }

                    }else{




                        $posts=Posts::where('category',$u->category)
                        ->where('userid',$use->id)
                        ->orderBy('created_at','DESC')->paginate(100);
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
                                                            "avatar"=>$use->avatar,
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
                                                    array_push($disposts,$FullPostData);






                        }




                    }


                }



                return MainResource::collection(["discoveryposts"=>$disposts]);
            }


        }

        function newPost(Request $req){
            $userid=auth('api')->user()->id;
            $post= new Posts();
            $post->userid=$userid;
            $post->postTitle=$req->posttitle;

            // if($req->hasFile('image')){
            //     $file=$req->file('image');
            //     $extension= $file->getClientOriginalExtension();
            //     $filename=time().'.'.$extension;
            //     $file->move('data'.$userid,$filename);
            //     $post->post=$filename;


            // }else{
            //     $post->post='';
            // }
            $post->post=$req->post;
            $post->likesCounter='0';
            $post->commentsCounter='0';
            $post->category=$req->category;
            $post->save();

                if($post){
                    return json_encode(true);
                }else{
                    return json_encode(false);
                }

    }


    function showPost($postid){
        $userid=auth('api')->user()->id;
        $user =Users::where('id',$userid)->get();

        $getpost=Posts::where('postid',$postid)
        ->get();
        $post=[];
        foreach($getpost as $gp){
            array_push($post,$gp);
        }


        return MainResource::collection(["userinfo"=>$user,"post"=>$post]);

    }


    function editPost($postid){
        $getposts=Posts::where('postid',$postid)
        ->get();
        $posts=[];
        foreach($getposts as $post){
            array_push($posts,$post);
        }
        return json_encode(true);
        // return view('edit_post',compact('posts'));
    }


function updatePost($postid,Request $req){
    $userid=auth('api')->user()->id;
    $post=Posts::find($postid);
    $post->postTitle=$req->posttitle;
    if($req->hasFile('image')){
        $file=$req->file('image');
        $extension= $file->getClientOriginalExtension();
        $filename=time().'.'.$extension;
        $file->move('data'.$userid,$filename);
        $post->post=$filename;


    }else{
        $post->post='';
    }
    $post->category=$req->category;
    $post->save();
    // return redirect()->back();
    if($post){
        return json_encode(true);
    }else{
        return json_encode(false);
    }
}

function deletePost($postid){
    $userid=auth('api')->user()->id;
    $delPost=Posts::findOrFail($postid);
    $post=null;
    $Tpost=Posts::where('postid', $postid)->get();
    foreach($Tpost as $filename){
        $post=$filename->post;
    }
    if($delPost){

        unlink(public_path("data".$userid."/").$post);

        $delPost->delete();
        return redirect()->back();
    }else{
        dd($delPost);
    }
}

     function likePost($postid){
        $userid=auth('api')->user()->id;
        $like=likes::create([
            'postid'=>$postid ,
            'userid'=>$userid
            ]) ;

            if($like){
                $postLikes=Posts::where('postid',$postid)->get();
                foreach($postLikes as $pl){
                    $increaseLike=$pl->likesCounter+1;
                    DB::table('posts')
                    ->where('postid',$postid)
                    ->update(['likesCounter'=>$increaseLike]);
                    // return redirect()->back();
                    return json_encode(true);
                }

            }else{
                return json_encode(false);
            }

    }

    function unlikePost($postid){
        $userid=auth('api')->user()->id;
        $unlike=likes::where('postid',$postid)
        ->where('userid',$userid)->delete();

            if($unlike){
                $postLikes=Posts::where('postid',$postid)->get();
                foreach($postLikes as $pl){
                    $decreaseLike=$pl->likesCounter-1;
                    DB::table('posts')
                    ->where('postid',$postid)
                    ->update(['likesCounter'=>$decreaseLike]);
                    return json_encode(true);
                }

            }else{
                // dd("Something went wrong!!");
                return json_encode(false);
            }

    }

    function commentPost(Request $req){
            $userid=auth('api')->user()->id;
            $postid=$req->postid;
            $comment= Comments::create([
            'postid'=>$postid ,
            'userid'=>$userid,
            'comment'=>$req->comment
            ]) ;
            if($comment){
                $postComments=Posts::where('postid',$postid)->get();
                foreach($postComments as $pc){
                    $increaseComments=$pc->commentsCounter;
                    $increaseComments++;
                    DB::table('posts')
                    ->where('postid',$postid)
                    ->update(['commentsCounter'=>$increaseComments]);
                    // return redirect()->back();
                    return json_encode(true);
                }

            }else{
                // dd("Something went wrong!!");
                return json_encode(false);
            }

    }

    function listComments($postid){
        $postComments=Posts::where('postid',$postid)->get();
        $comments=[];
        foreach($postComments as $pc){
            foreach($pc->comment as $pcc){
            $FullCommentData=array(
                "postid"=>$pcc->postid,
                "avatar"=>$pcc->user->avatar,
                "userid"=>$pcc->userid,
                "username"=>$pcc->user->firstname,
                "comment"=>$pcc->comment
                );
            array_push($comments,$FullCommentData);
            }
        }
        return MainResource::collection(["postComments"=>$comments]);

    }

    function deleteComment($commentid,$postid){
        $delComment=Comments::where('commentid',$commentid)->delete();
            if($delComment){
                $postComments=Posts::where('postid',$postid)->get();
                foreach($postComments as $pc){
                    $decreaseComments=$pc->commentsCounter;
                     $decreaseComments--;
                    DB::table('posts')
                    ->where('postid',$postid)
                    ->update(['commentsCounter'=>$decreaseComments]);
                    // return redirect()->back();
                    return json_encode(true);
                }

            }else{
                // dd("Something went wrong!!");
                return json_encode(false);
            }
    }

    function editComment($commentid,Request $req){
        auth('api')->user()->id;
        DB::table('comments')
        ->where('commentid',$commentid)
        ->update(['comment'=>$req->comment]);
        return json_encode(true);

    }

 function Report($Accuseduserid,$postid,$commentid,$reporta,Request $req){
    $Compuserid=auth('api')->user()->id;
    $report=Reports::create([
        'compUserid'=>$Compuserid ,
        'userid'=>$Accuseduserid,
        'postid'=>$postid,
        'commentid'=>$commentid,
        'reports'=>$reporta,
        'description'=>$req->description
        ]) ;

        if($report){
            return json_encode(true);
        }else{
            return json_encode(false);
        }
 }
 
 function addPhoto(Request $req){
            $userid=auth('api')->user()->id;

            if($req->hasFile('image')){
                $file=$req->file('image');
                $extension= $file->getClientOriginalExtension();
                $filename=time().'.'.$extension;
                $file->move('data'.$userid,$filename);
                return json_encode($filename);


            }else{
                return json_encode(false);
            }
     
 }

}
