<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    public $timestamps = false;

    // In User.php and other related models
    protected $connection = 'login';
    
    protected $table = 'login.positions';
    
    protected $primaryKey = 'pos_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pos_code',
        'pos_name',
        'pos_is_active',
        'pos_created_at',
        'pos_created_by',
        'pos_updated_at',
        'pos_updated_by',
    ];

    protected $casts = [
        'pos_created_at' => 'datetime',
        'pos_updated_at' => 'datetime',
    ];
    
    /**
     * The users that belong to the position.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'u_position_id', 'pos_id');
    }
}
