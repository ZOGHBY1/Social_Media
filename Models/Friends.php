<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Friends extends Model{

    protected $primaryKey='id';
    protected $fillable= ['id','userid','userFriendid','requestStatus'];

    public function user(){

        return $this->belongsTo(User::class,'userid');

    }
    public function userfriend(){

        return $this->belongsTo(User::class,'userFriendid');

    }




}
