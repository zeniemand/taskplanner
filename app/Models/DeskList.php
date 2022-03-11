<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'desk_id'];

    public function desk()
    {
        return $this->belongsTo(Desk::class, 'desk_id');
    }

    public function card(){
        return $this->hasOne(DeskList::class);
    }

    public function cards(){
        return $this->hasMany(Card::class);
    }
}
