<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['title', 'description', 'date', 'status'];

    public function reports() {
        return $this->hasMany(Report::class);
    }

}
