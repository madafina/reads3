<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable 
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function divisions()
    {
        // Seorang User (Dosen) bisa bertugas di banyak Divisi
        return $this->belongsToMany(Division::class, 'division_staff');
    }

    public function adminlte_image()
    {
        // Contoh logika: jika user punya profil residen/dosen dan ada foto, tampilkan.
        // Jika tidak, tampilkan gambar default dari Gravatar.
        $photo = $this->resident->photo ?? $this->lecturer->photo ?? null;

        if ($photo) {
            // Asumsi foto disimpan di storage/app/public/photos
            return asset('storage/' . $photo);
        }

        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email)));
    }

    public function adminlte_desc()
    {
        return $this->getRoleNames()->first() ?? 'User';
    }

    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }

}