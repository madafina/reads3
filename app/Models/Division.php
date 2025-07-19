<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function staff()
    {
        // Sebuah Divisi memiliki banyak Staf (User)
        return $this->belongsToMany(User::class, 'division_staff');
    }

    public function requirementRules()
    {
        return $this->hasMany(RequirementRule::class);
    }
}
