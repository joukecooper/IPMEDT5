<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeederTimer;
use App\Models\StorageLevel;

class FeederTimerController extends Controller
{
    
    // Functie om een nieuwe timer aan te maken
    public function create_new_timer(Request $request){

        // Maak een nieuw timer object aan
        $nieuwe_timer = new FeederTimer();

        // Bepaal het huidige uur en bereken het nieuwe uur voor de timer
        $huidig_uur = date('H');
        $nieuw_uur = ($huidig_uur - 1 + 24) % 24;
        $nieuwe_tijd = str_pad($nieuw_uur, 2, '0', STR_PAD_LEFT) . ':' . date('i');
        $nieuwe_timer->tijd = $nieuwe_tijd;

        // Stel de dagen van de week en de status van de voedertijd in
        $nieuwe_timer->dagen_van_de_week = '';
        $nieuwe_timer->feed_time_is_active = true;

        // Sla de nieuwe timer op in de database
        $nieuwe_timer->save();
        
        // Geef een succesbericht terug
        return response()->json(['bericht' => 'Timer is aangemaakt'], 200);
    }

    // Functie om een timer te verwijderen
    public function delete_timer(Request $request){

        // Haal de timer-ID op uit het verzoek
        $timer_id = $request->input('timer_id');

        // Controleer of de timer bestaat
        $bestaat = FeederTimer::where('timer_id', $timer_id)->exists();

        // Als de timer niet bestaat, geef een foutmelding terug
        if (!$bestaat){

            return response()->json(['fout' => 'Timer bestaat niet'], 400);

        } else {

            // Verwijder de timer uit de database
            FeederTimer::where('timer_id', $timer_id)->delete();
            return response()->json(['bericht' => 'Timer is verwijderd'], 200);

        }

    }

    // Functie om een timer bij te werken
    public function update_timer(Request $request){
        $timer_id = $request->input('timer_id');
    
        $timer = FeederTimer::where('timer_id', $timer_id)->first();
    
        if (!$timer){
            return response()->json(['fout' => 'Timer bestaat niet'], 400);
        } else {
            // Update de tijd en dagen van de week van de timer
            $timer->tijd = $request->input('tijd');
            $timer->dagen_van_de_week = $request->input('dagen_van_de_week');
    
            // Sla de wijzigingen op
            $timer->save();
            return response()->json(['bericht' => 'Timer is bijgewerkt'], 200);
        }
    }

    // Functie om de actieve status van een timer bij te werken
    public function update_timer_active(Request $request){
        $timer_id = $request->input('timer_id');
    
        $timer = FeederTimer::where('timer_id', $timer_id)->first();
    
        if (!$timer){
            return response()->json(['fout' => 'Timer bestaat niet'], 400);
        } else {
            // Update de actieve status van de timer
            $timer->feed_time_is_active = $request->input('feed_time_is_active');
    
            // Sla de wijzigingen op
            $timer->save();
            return response()->json(['bericht' => 'Timer is bijgewerkt'], 200);
        }
    }

    // Functie om alle timers op te halen
    public function get_timers(Request $request){
        $timers = FeederTimer::get();
        return response()->json(['bericht' => $timers], 200);
    }

    // Functie om de timers te controleren en het voeren te activeren indien nodig
    public function check_timers(Request $request){
        $huidige_tijd = now()->format('H:i');
    
        // Haal alle actieve timers op
        $timers = FeederTimer::where('feed_time_is_active', true)->get();
    
        foreach ($timers as $timer) {
            // Controleer of de timer actief is op de huidige dag
            if ($this->isCurrentDayActive($timer->dagen_van_de_week)) {
                $opgeslagen_tijd = date('H:i', strtotime($timer->tijd));
    
                // Vergelijk de huidige tijd met de opgeslagen tijd van de timer
                $huidige_uur_minuten = substr($huidige_tijd, 0, 5);
                $opgeslagen_uur_minuten = substr($opgeslagen_tijd, 0, 5);
    
                // Als de tijden overeenkomen, activeer het voeren
                if ($huidige_uur_minuten === $opgeslagen_uur_minuten) {
                    $this->triggerAlarm($timer);
                    return response()->json(['bericht' => 'voer nu = 1'], 200);
                }
            }
        }
    
        // Als er geen timers zijn geactiveerd, geef een bericht terug
        return response()->json(['bericht' => 'Geen geactiveerde timers'], 200);
    }
    
    // Privéfunctie om te controleren of de huidige dag actief is voor de timer
    private function isCurrentDayActive($dagenVanDeWeek){
        $huidigeDag = strtolower(now()->format('l'));
        $actieveDagen = explode(';', strtolower($dagenVanDeWeek));
        $isDagActief = in_array($huidigeDag, $actieveDagen);
        return $isDagActief;
    }
    
    // Privéfunctie om het voeren te activeren
    private function triggerAlarm($timer){
        $opslagniveau = StorageLevel::first();
        $opslagniveau->feed_now = 1;
        $opslagniveau->save();
    }
    
}
