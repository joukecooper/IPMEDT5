<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageLevel;

class StorageLevelController extends Controller
{
    
    public function set_storage_level(Request $request){

        $storage_level = StorageLevel::first();

        $storage_level->fill_level = $request->input('fill_level');
        $storage_level->save();

        return response()->json(['message' => 'Storage level updated'], 200);

    }

    public function get_storage_level(Request $request){

        $storage_level = StorageLevel::first();
        return response()->json(['message' => $storage_level->fill_level], 200);

    }


    public function set_feed_now(Request $request){

        $storage_level = StorageLevel::first();

        $storage_level->feed_now = $request->input('feed_now');
        $storage_level->save();

        return response()->json(['message' => 'Storage level updated'], 200);

    }

    public function get_feed_now(Request $request){

        $storage_level = StorageLevel::first();
        return response()->json(['message' => $storage_level->feed_now], 200);

    }

}
