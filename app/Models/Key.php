<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'api_key',
        'status'
    ];

    public function groups(){
        return $this->hasMany(Group::class, 'key_id', 'id');
    }

}
