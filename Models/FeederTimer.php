<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeederTimer extends Model
{

    use HasFactory;
    protected $table = 'feeder_timers';
    protected $primaryKey = 'timer_id';

}
