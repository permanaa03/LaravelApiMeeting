<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class posts extends Model
{
    protected $table = 'posts';
    protected $fillable = ['id','user_id','desc','photo'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
