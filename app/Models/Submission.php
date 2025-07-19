<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'presentation_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function supervisor() // Dosen Pembimbing
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function verifier() // Admin yang memverifikasi
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}