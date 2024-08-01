<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    // テーブル名を指定
    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',

    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
