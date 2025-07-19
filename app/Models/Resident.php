<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentStage()
    {
        return $this->belongsTo(Stage::class, 'current_stage_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Untuk mengakses riwayat tahap (many-to-many)
    public function stageHistory()
    {
        return $this->belongsToMany(Stage::class, 'resident_stage')
            ->withPivot('start_date', 'end_date', 'status')
            ->withTimestamps();
    }
}