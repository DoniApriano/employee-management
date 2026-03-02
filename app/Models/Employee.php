<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'position',
        'photo',
        'address',
    ];

    public function photoUrl()
    {
        return $this->photo ?
            (Storage::disk('public')->exists($this->photo) ?
                url(Storage::url($this->photo)) :
                asset('assets/dummy/dummy-avatar.jpeg')) :
            asset('assets/dummy/dummy-avatar.jpeg');
    }
}
