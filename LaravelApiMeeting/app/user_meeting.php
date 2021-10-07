<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_meeting extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
