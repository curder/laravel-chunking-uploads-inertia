<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'file_path'
    ];

    public function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk('public')->url($this->file_path));
    }
}
