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
        // Relasi ke User (Dosen) melalui tabel pivot division_staff
        return $this->belongsToMany(User::class, 'division_staff')->withPivot('is_pj')->withTimestamps();
    }

    public function requirementRules()
    {
        return $this->hasMany(RequirementRule::class);
    }

    public function residents()
    {
        return $this->belongsToMany(Resident::class, 'submissions')->distinct();
    }
}
