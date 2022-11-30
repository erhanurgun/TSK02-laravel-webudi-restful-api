<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User.
 * @OA\Schema(title="User model", description="User model")
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Property(title="name", description="enter name", example="Demo User")
     * @var string
     */
    protected $name;

    /**
     * @OA\Property(title="email", description="enter email", example="demo@urgun.com.tr")
     * @var string
     */
    protected $email;

    /**
     * @OA\Property(title="password", description="enter password", example="Demo1234!")
     * @var string
     */
    protected $password;

    /**
     * @OA\Property(title="phone", description="enter phone", example="+90 (555) 555 55 55")
     * @var string
     */
    protected $phone;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // key type string
    protected $keyType = 'string';

    // create uuid for user
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)\Str::uuid();
        });
    }
}
