<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Organisasi;
use App\Models\Kelas;
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    protected $fillable = [
    'username',
    'nama',
    'password',
    'password_encrypted',
    'password_rotated_at',
    'password_rotation_year',
    'role',
    'status',
    'jenis_akun',
    'kelas_id',
    'organisasi_id',
    'security_question',
    'security_answer',
    'security_answer_updated_at',
];

    protected $hidden = [
    'password',
    'remember_token',
    'password_encrypted',
    'security_answer',
];

    protected function casts(): array
{
    return [
        'password' => 'hashed',
        'password_rotated_at' => 'datetime',
        'security_answer_updated_at' => 'datetime',
    ];
}
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
    public function organisasi(): BelongsTo
    {
        return $this->belongsTo(Organisasi::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
*/
    
}
