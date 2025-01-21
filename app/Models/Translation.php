<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'content',
        'language_id'
    ];

    public function language(){
        $this->belongsTo(Language::class);
    }

    public function tags(){
        $this->belongsToMany(Tag::class);
    }
}
