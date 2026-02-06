<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'status'];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function involvedEmployees()
    {
        return $this->belongsToMany(User::class);
    }
}
