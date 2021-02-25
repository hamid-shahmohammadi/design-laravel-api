<?php 
namespace App\Models\Traits;

use App\Models\Like;

trait Likeable{
    public function likes(){
        return $this->morphMany(Like::class,'likeable');
    }

    public function like(){
        if(! auth()->check()){
            return;
        }

        if($this->isLikedByUser(auth()->id())){
            return;
        }

        $this->likes()->create(['user_id',auth()->id()]);
    }
    public function isLikedByUser($user_id){
        return (bool)$this->likes()
            ->where('user_id',$user_id)
            ->count();
    }
}