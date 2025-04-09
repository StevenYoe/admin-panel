<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    // Disable Laravel's automatic timestamp handling
    public $timestamps = false;
    
    protected $connection = 'login';
    
    protected $table = 'login.users';
    
    protected $primaryKey = 'u_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'u_employee_id',
        'u_name',
        'u_email',
        'u_password',
        'u_phone',
        'u_address',
        'u_birthdate',
        'u_join_date',
        'u_profile_image',
        'u_division_id',
        'u_position_id',
        'u_is_manager',
        'u_manager_id',
        'u_is_active',
        'u_created_at',
        'u_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'u_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'u_created_at' => 'datetime',
        'u_updated_at' => 'datetime',
    ];
    
    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'login.user_roles', 'ur_user_id', 'ur_role_id');
    }
    
    /**
     * Check if the user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }

    /**
     * Get the division that owns the user.
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'u_division_id', 'div_id');
    }

    /**
     * Get the position that owns the user.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'u_position_id', 'pos_id');
    }

    /**
     * Get the manager user.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'u_manager_id', 'u_id');
    }
}