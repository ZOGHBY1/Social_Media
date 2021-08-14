<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;
use App\Models\Likes;
use App\Models\Posts;
use App\Models\User as Users;
use App\Models\Comments;
use App\Models\Reports;
use Illuminate\Support\Facades\Auth;

class postController extends Controller{

        function newPost(Request $req){
            $userid=Auth::user()->id;
            $post=Posts::create([
                'userid'=>$userid ,
                'postTitle'=>$req->postT,
                'post'=>'noattachment',
                'likesCounter'=>'0',
                'commentsCounter'=>'0',
                'category'=>$req->category,
                ]) ;
                // dd($userid." ".$req->postT);
                if($post){
                    return redirect()->back();
                }else{
                    dd($post);
                }

    }


    function showPost($postid){
        $userid=Auth::user()->id;
        $user =Users::where('id',$userid)->get();

        $getpost=Posts::where('postid',$postid)
        ->get();
        $post=[];
        foreach($getpost as $gp){
            array_push($post,$gp);
        }


           // dd($post);
        return view('view_post',compact(['user'],['post']));

    }


    function editPost($postid){
        $getposts=Posts::where('postid',$postid)
        ->get();
        $posts=[];
        foreach($getposts as $post){
            array_push($posts,$post);
        }
        return view('edit_post',compact('posts'));
    }


function updatePost($postid,Request $req){
$req->validate([
    'posttitle' => 'required|unique:posts|max:255',
    'post'=>'required'
]);
    $post=Posts::find($postid);
    $post->postTitle=$req->posttitle;
    $post->post=$req->post;
    $post->save();


    //$post->save($req)->all();

   // dd($req);
    return redirect()->back();
}

function deletePost($postid){
    $delPost=Posts::where('postid',$postid)->delete();

    if($delPost){
        return redirect()->back();
    }else{
        dd($delPost);
    }
}

     function likePost($postid,$userid){
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
                    return redirect()->back();
                }

            }

    }

    function unlikePost($postid,$userid){
        $unlike=likes::where('postid',$postid)
        ->where('userid',$userid)->delete();

            if($unlike){
                $postLikes=Posts::where('postid',$postid)->get();
                foreach($postLikes as $pl){
                    $decreaseLike=$pl->likesCounter-1;
                    DB::table('posts')
                    ->where('postid',$postid)
                    ->update(['likesCounter'=>$decreaseLike]);
                    return redirect()->back();
                }

            }else{
                dd("Something went wrong!!");
            }

    }

    function commentPost($postid,Request $req){
            $userid=Auth::user()->id;
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
                    return redirect()->back();
                }

            }else{
                dd("Something went wrong!!");
            }

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
                    return redirect()->back();
                }

            }else{
                dd("Something went wrong!!");
            }
    }

    function editComment($postid,Request $req){
        $userid=Auth::user()->id;
        DB::table('comments')
        ->where('postid',$postid)->where('userid',$userid)
        ->update(['comment'=>$req->comment]);
        return redirect()->back();

    }

    function Report($Accuseduserid,$postid,$commentid,$reporta,Request $req){
        $Compuserid=Auth::user()->id;
        $report=Reports::create([
            'compUserid'=>$Compuserid ,
            'userid'=>$Accuseduserid,
            'postid'=>$postid,
            'commentid'=>$commentid,
            'reports'=>$reporta,
            'description'=>$req->description
            ]) ;

            if($report){
                return redirect()->back();
            }else{
                return redirect()->back();
            }
     }


}
