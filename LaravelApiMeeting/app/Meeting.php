<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\user_meeting;

class Meeting extends Model
{
    protected $fillable = ['title','description','time'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function user_meeting()
    {
        return $this->belongsToMany(meeting::class);
    }

}
