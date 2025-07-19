<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Relasi many-to-many untuk kondisi "ATAU"
    public function taskCategories()
    {
        return $this->belongsToMany(TaskCategory::class, 'requirement_rule_category');
    }
}