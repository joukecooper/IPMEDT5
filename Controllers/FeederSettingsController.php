<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeederSettings;

class FeederSettingsController extends Controller
{
    
    public function increase_food_per_meal(){

        $feeder_settings = FeederSettings::first();

        $feeder_settings->food_per_meal_cycles++;
        $feeder_settings->save();

        return response()->json(['message' => 'food per meal has been increased'], 200);

    }

    public function decrease_food_per_meal(){

        $feeder_settings = FeederSettings::first();

        if ($feeder_settings->food_per_meal_cycles != 1){

            $feeder_settings->food_per_meal_cycles--;
            $feeder_settings->save();

            return response()->json(['message' => 'food per meal has been decreased'], 200);
        
        }

        return response()->json(['error' => 'Bird need food :('], 202);

    }   

    public function get_amount_of_food(){

        $feeder_settings = FeederSettings::first();

        $amount_of_food = $feeder_settings->food_per_meal_cycles * 2.5;

        return response()->json(['doubleValue' => $amount_of_food]);

    }

}
