<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model{

    protected $primaryKey='reportid';
    protected $fillable=[
        'reportid','compUserid','userid','postid','commentid','reports','description'
    ];

    public function compuser(){

        return $this->belongsTo(User::class,'compUserid');
    }

    public function user(){

        return $this->belongsTo(User::class,'userid');

    }


}
