<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id_online',
        'name',
        'key_id'
    ];

    public function api(){
        return $this->hasOne(Key::class, 'id', 'key_id');
    }

    public function subscribers(){
        return $this->hasMany(Subscriber::class, 'group_id', 'id');
    }

}
