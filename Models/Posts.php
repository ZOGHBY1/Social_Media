<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model{

    protected $primaryKey='postid';
    protected $fillable=[
        'postid','userid','postTitle','post','likesCounter','commentsCounter','category'
    ];

    public function user(){

        return $this->belongsTo(User::class,'userid');
    }

    public function likes(){

        return $this->hasMany(Likes::class,'postid');
    }

    public function comment(){

        return $this->hasMany(Comments::class,'postid');
    }

    public function report(){

        return $this->hasMany(Reports::class,'postid');

    }
}
