<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'country',
        'group_id',
        'sub_date',
        'sub_time',
    ];

    public function group(){
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

}
