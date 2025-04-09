<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    // In User.php and other related models
    protected $connection = 'login';
    
    protected $table = 'login.roles';
    
    protected $primaryKey = 'role_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'role_name',
        'role_level',
        'role_is_active',
        'role_created_at',
        'role_created_by',
        'role_updated_at',
        'role_updated_by'
    ];
    
    protected $casts = [
        'role_created_at' => 'datetime',
        'role_updated_at' => 'datetime',
    ];
    
    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'login.user_roles', 'ur_role_id', 'ur_user_id');
    }
}