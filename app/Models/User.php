<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'profile', 'type', 'phone',
        'address', 'dob',
        'create_user_id', 'updated_user_id', 'deleted_user_id',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 1, // user role
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
/*     protected $casts = [
'created_at' => 'datetime:Y-m-d',
]; */

/*     public function getCreatedAtAttribute($value)
{
return date('d.m.Y H:i', strtotime($value));
} */

}
