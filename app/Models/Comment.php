<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    const CREATED_AT = 'commented_at';

    protected $fillable = [
        'user_id',
        'image_id',
        'comment',
        'parent_id', // Убедитесь, что 'parent_id' указано здесь
    ];
    

    protected $dates = [
        'commented_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies');
    }
    // Возможные дополнительные методы и связи 
}
