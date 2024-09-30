<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identification_result',
        'photo_path',
        'reported_at',
        'latitude',
        'longitude',
        'status_id'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(ReportStatus::class);
    }

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'report_plant');
    }
}
