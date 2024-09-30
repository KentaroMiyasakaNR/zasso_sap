<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_plant');
    }
}
