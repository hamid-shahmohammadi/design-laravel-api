<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use Taggable,Likeable;
    protected $fillable=[
        'user_id',
        'team_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_success',
        'disk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getImagesAttribute()
    {
        return [
            'thumbnail'=>$this->getImagePath('thumbnail'),
            'large'=>$this->getImagePath('large'),
            'original'=>$this->getImagePath('original')
        ];
    }

    public function getImagePath($size)
    {
        return url("uploads/{$size}/".$this->image);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class,'commentable')->orderBy('created_at','asc');
    }
}
