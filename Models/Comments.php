<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model{
    protected $primaryKey='commentid';
    protected $fillable= ['commentid','postid','userid','comment'];

    public function post(){

        return $this->belongsTo(Posts::class,'postid');

    }
    public function user(){

        return $this->belongsTo(User::class,'userid');

    }

    public function report(){

        return $this->hasMany(Reports::class,'commentid');

    }



}
