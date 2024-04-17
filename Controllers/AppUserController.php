<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppUser;

class AppUserController extends Controller
{
    // Functie om een nieuwe gebruiker te registreren
    public function register_user(Request $request){

        // Haal de gebruikersnaam en wachtwoord op uit het verzoek
        $gebruikersnaam = $request->input('gebruikersnaam');
        $wachtwoord = $request->input('wachtwoord');

        // Controleer of de gebruikersnaam al bestaat in de database
        $bestaat = AppUser::where('gebruikersnaam', $gebruikersnaam)->exists();

        // Als de gebruikersnaam al bestaat, geef dan een foutmelding terug
        if ($bestaat) {
            return response()->json(['fout' => 'Gebruiker bestaat al'], 400);
        } else {
            // Als de gebruikersnaam niet bestaat, maak een nieuwe gebruiker aan
            $nieuwe_gebruiker = new AppUser();

            // Stel de gebruikersnaam en wachtwoord in voor de nieuwe gebruiker
            $nieuwe_gebruiker->gebruikersnaam = $gebruikersnaam;
            $nieuwe_gebruiker->wachtwoord = $wachtwoord;

            // Sla de nieuwe gebruiker op in de database
            $nieuwe_gebruiker->save();

            // Geef een succesvolle reactie terug
            return response()->json([], 201);
        }
    }

    // Functie om een gebruiker te authenticeren en in te loggen
    public function login_user(Request $request){

        // Haal de gebruikersnaam en wachtwoord op uit het verzoek
        $gebruikersnaam = $request->input('gebruikersnaam');
        $wachtwoord = $request->input('wachtwoord');
    
        // Controleer of de gebruikersnaam bestaat in de database
        $bestaat = AppUser::where('gebruikersnaam', $gebruikersnaam)->exists();
    
        // Als de gebruikersnaam niet bestaat, geef dan een foutmelding terug
        if (!$bestaat) {
            return response()->json(['fout' => 'Gebruiker bestaat niet'], 400);
        } else {
            // Als de gebruikersnaam bestaat, controleer of het opgegeven wachtwoord overeenkomt met het opgeslagen wachtwoord
            $match = AppUser::where('gebruikersnaam', $gebruikersnaam)
                            ->where('wachtwoord', $wachtwoord)
                            ->exists();
            
            // Als het wachtwoord niet overeenkomt, geef dan een foutmelding terug
            if (!$match){
                return response()->json(['fout' => 'Onjuist wachtwoord'], 400);
            } else {
                // Als het wachtwoord overeenkomt, is de authenticatie succesvol, geef een succesvolle reactie terug
                return response()->json([], 201);
            }
        }
    }
}
