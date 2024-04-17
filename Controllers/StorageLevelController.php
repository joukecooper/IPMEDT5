<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageLevel;

class StorageLevelController extends Controller
{
    
    // Functie om het opslagniveau in te stellen
    public function set_storage_level(Request $request){

        // Haal het opslagniveau op uit de database
        $opslagniveau = StorageLevel::first();

        // Update het opslagniveau met de nieuwe waarde uit het verzoek
        $opslagniveau->fill_level = $request->input('fill_level');
        $opslagniveau->save();

        // Geef een succesbericht terug
        return response()->json(['bericht' => 'Opslagniveau bijgewerkt'], 200);

    }

    // Functie om het opslagniveau op te halen
    public function get_storage_level(Request $request){

        // Haal het opslagniveau op uit de database
        $opslagniveau = StorageLevel::first();

        // Geef het opslagniveau terug als JSON-bericht
        return response()->json(['bericht' => $opslagniveau->fill_level], 200);

    }

    // Functie om de feed-now-status in te stellen
    public function set_feed_now(Request $request){

        // Haal het opslagniveau op uit de database
        $opslagniveau = StorageLevel::first();

        // Update de feed-now-status met de nieuwe waarde uit het verzoek
        $opslagniveau->feed_now = $request->input('feed_now');
        $opslagniveau->save();

        // Geef een succesbericht terug
        return response()->json(['bericht' => 'Opslagniveau bijgewerkt'], 200);

    }

    // Functie om de feed-now-status op te halen
    public function get_feed_now(Request $request){

        // Haal de feed-now-status op uit de database
        $opslagniveau = StorageLevel::first();

        // Geef de feed-now-status terug als JSON-bericht
        return response()->json(['bericht' => $opslagniveau->feed_now], 200);

    }

}
