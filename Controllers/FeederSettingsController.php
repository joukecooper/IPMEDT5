<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeederSettings;

class FeederSettingsController extends Controller
{
    
    // Functie om de hoeveelheid voedsel per maaltijd te verhogen
    public function increase_food_per_meal(){

        // Haal de instellingen van de voederautomaat op uit de database
        $voederautomaat_instellingen = FeederSettings::first();

        // Verhoog het aantal cycli voor voedsel per maaltijd
        $voederautomaat_instellingen->food_per_meal_cycles++;
        $voederautomaat_instellingen->save();

        // Geef een succesbericht terug
        return response()->json(['bericht' => 'hoeveelheid voedsel per maaltijd is verhoogd'], 200);

    }

    // Functie om de hoeveelheid voedsel per maaltijd te verlagen
    public function decrease_food_per_meal(){

        // Haal de instellingen van de voederautomaat op uit de database
        $voederautomaat_instellingen = FeederSettings::first();

        // Controleer of het aantal cycli voor voedsel per maaltijd niet al op 1 staat
        if ($voederautomaat_instellingen->food_per_meal_cycles != 1){

            // Verlaag het aantal cycli voor voedsel per maaltijd
            $voederautomaat_instellingen->food_per_meal_cycles--;
            $voederautomaat_instellingen->save();

            // Geef een succesbericht terug
            return response()->json(['bericht' => 'hoeveelheid voedsel per maaltijd is verlaagd'], 200);
        
        }

        // Als het aantal cycli al op 1 staat, geef een foutmelding terug
        return response()->json(['fout' => 'Vogel heeft voedsel nodig :('], 202);

    }   

    // Functie om de hoeveelheid voedsel op te vragen
    public function get_amount_of_food(){

        // Haal de instellingen van de voederautomaat op uit de database
        $voederautomaat_instellingen = FeederSettings::first();

        // Bereken de hoeveelheid voedsel op basis van het aantal cycli voor voedsel per maaltijd
        $hoeveelheid_voedsel = $voederautomaat_instellingen->food_per_meal_cycles * 2.5;

        // Geef de hoeveelheid voedsel terug
        return response()->json(['doubleValue' => $hoeveelheid_voedsel]);

    }

}
