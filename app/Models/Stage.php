<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function residents()
    {
        return $this->hasMany(Resident::class, 'current_stage_id');
    }

    public function requirementRules()
    {
        return $this->hasMany(RequirementRule::class);
    }
}