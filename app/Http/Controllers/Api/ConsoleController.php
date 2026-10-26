<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsoleMasterModel;
use Illuminate\Support\Facades\Storage;

class ConsoleController extends Controller
{
    // Get PS2 console
  /*  public function ps2()
    {
       dd("PS2 method called.");
        $console = ConsoleMasterModel::where('console_name', 'PS2')->first();

        if (!$console) {
            return response()->json(['success' => false, 'message' => 'PS2 not found'], 404);
        }
  
        return response()->json([
            'success' => true,
            'data' => [
                'id'             => $console->id,
                'console_name'   => $console->console_name,
                'limited_person' => $console->limited_person,
                'console_icon'   => $console->console_icon
                                    ? asset('storage/console_icons/'.$console->console_icon)
                                    : null,
                'status'         => $console->status
            ]
        ]);
    }
*/
//For single console
/*public function show($consoleName)
{
    $console = ConsoleMasterModel::whereRaw('LOWER(console_name) = ?', [strtolower($consoleName)])->first();

    if (!$console) {
        return response()->json([
            'success' => false,
            'message' => "$consoleName not found"
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id'             => $console->id,
            'console_name'   => $console->console_name,
            'limited_person' => $console->limited_person,
            'console_icon'   => $console->console_icon
                                  ? asset('storage/icons/'.$console->console_icon)
                                  : null,
            'status'         => $console->status,
        ]
    ]);
}*/
//For all consoles
public function index(Request $request)
{
    // Get place_id from header
    $placeId = $request->header('place-id');

    if (!$placeId) {
        return response()->json([
            'success' => false,
            'message' => 'place_id required'
        ], 422);
    }

    // Get all active consoles for this place
    $consoles = ConsoleMasterModel::where('place_id', $placeId)
        ->where('status', 1)
        ->orderBy('sort_order', 'asc')
        ->get();

    $data = [];

    foreach ($consoles as $console) {
        $data[] = [
            'id'             => $console->id,
            'console_name'   => $console->console_name,
            'limited_person' => $console->limited_person,
            'console_icon'   => $console->console_icon
                ? asset('public/' . $console->console_icon)
                : null,
            'status'         => $console->status,
        ];
    }

    return response()->json([
        'success' => true,
        'data'    => $data
    ]);
}

public function update(Request $request, $id)
{
    $placeId = $request->header('place-id');

if (!$placeId) {
    return response()->json([
        'success' => false,
        'message' => 'place_id required'
    ], 422);
}

   $console = ConsoleMasterModel::where('id', $id)
    ->where('place_id', $placeId)
    ->first();

    if (!$console) {
        return response()->json(['success' => false, 'message' => 'Console not found'], 404);
    }

    $console->console_name   = $request->console_name;
    $console->limited_person = $request->limited_person;
    $console->status         = $request->status;

    // ✅ If file uploaded, store in /public/icons
    if ($request->hasFile('console_icon')) {
        $path = $request->file('console_icon')->store('icons', 'public');
        $console->console_icon = $path; // only path saved in DB
    }

    $console->save();

    return response()->json([
        'success' => true,
        'data' => [
            'id'             => $console->id,
            'console_name'   => $console->console_name,
            'limited_person' => $console->limited_person,
            'console_icon'   => $console->console_icon 
                                ? asset('storage/'.$console->console_icon) 
                                : null,
            'status'         => $console->status,
           
        ]
    ]);
}


}