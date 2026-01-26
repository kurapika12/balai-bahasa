<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'activity_id', 'title', 'type', 'file_path', 'description'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function activity() {
        return $this->belongsTo(Activity::class);
    }

}
