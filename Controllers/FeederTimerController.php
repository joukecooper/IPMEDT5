<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeederTimer;
use App\Models\StorageLevel;

class FeederTimerController extends Controller
{
    
    public function create_new_timer(Request $request){

        $new_user = new FeederTimer();

        $current_hour = date('H');
        $new_hour = ($current_hour - 1 + 24) % 24;
        $new_time = str_pad($new_hour, 2, '0', STR_PAD_LEFT) . ':' . date('i');
        $new_user->time = $new_time;

        $new_user->days_of_week = '';
        $new_user->feed_time_is_active = true;

        $new_user->save();
        return response()->json(['message' => 'Timer has been created'], 200);
    }

    public function delete_timer(Request $request){

        $timer_id = $request->input('timer_id');

        $exists = FeederTimer::where('timer_id', $timer_id)->exists();

        if (!$exists){

            return response()->json(['error' => 'Timer does not exist'], 400);

        } else {

            FeederTimer::where('timer_id', $timer_id)->delete();
            return response()->json(['message' => 'Timer has been deleted'], 200);

        }

    }

    public function update_timer(Request $request){
        $timer_id = $request->input('timer_id');
    
        $timer = FeederTimer::where('timer_id', $timer_id)->first();
    
        if (!$timer){
            return response()->json(['error' => 'Timer does not exist'], 400);
        } else {
            $timer->time = $request->input('time');
            $timer->days_of_week = $request->input('days_of_week');
    
            $timer->save();
            return response()->json(['message' => 'Timer has been updated'], 200);
        }
    }

    public function update_timer_active(Request $request){
        $timer_id = $request->input('timer_id');
    
        $timer = FeederTimer::where('timer_id', $timer_id)->first();
    
        if (!$timer){
            return response()->json(['error' => 'Timer does not exist'], 400);
        } else {
            $timer->feed_time_is_active = $request->input('feed_time_is_active');
    
            $timer->save();
            return response()->json(['message' => 'Timer has been updated'], 200);
        }
    }

    public function get_timers(Request $request){
        $timers = FeederTimer::get();
        return response()->json(['message' => $timers], 200);
    }

    public function check_timers(Request $request){
        $current_time = now()->format('H:i');
    
        $timers = FeederTimer::where('feed_time_is_active', true)->get();
    
        foreach ($timers as $timer) {
            if ($this->isCurrentDayActive($timer->days_of_week)) {
                $stored_time = date('H:i', strtotime($timer->time));
    
                $current_hours_minutes = substr($current_time, 0, 5);
                $stored_hours_minutes = substr($stored_time, 0, 5);
    
                if ($current_hours_minutes === $stored_hours_minutes) {
                    $this->triggerAlarm($timer);
                    return response()->json(['message' => 'feed now = 1'], 200);
                }
            }
        }
    
        return response()->json(['message' => 'No timers activated'], 200);
    }
    
    private function isCurrentDayActive($daysOfWeek){
        $currentDay = strtolower(now()->format('l'));
        $activeDays = explode(';', strtolower($daysOfWeek));
        $isDayActive = in_array($currentDay, $activeDays);
        return $isDayActive;
    }
    
    private function triggerAlarm($timer){
        $storage_level = StorageLevel::first();
        $storage_level->feed_now = 1;
        $storage_level->save();
    }
    
}
