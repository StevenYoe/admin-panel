<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    // In User.php and other related models
    protected $connection = 'login';
    
    protected $table = 'login.divisions';
    
    protected $primaryKey = 'div_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'div_code',
        'div_name',
        'div_is_active',
        'div_created_at',
        'div_created_by',
        'div_updated_at',
        'div_updated_by',
    ];

    protected $casts = [
        'div_created_at' => 'datetime',
        'div_updated_at' => 'datetime',
    ];
    
    /**
     * The users that belong to the division.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'u_division_id', 'div_id');
    }
}
