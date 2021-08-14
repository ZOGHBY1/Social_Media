<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model{
    protected $primaryKey='likeid';
    protected $fillable= ['likeid','postid','userid'];

    public function post(){

        return $this->belongsTo(Posts::class,'postid');

    }
    public function user(){

        return $this->belongsTo(User::class,'userid');

    }





}
