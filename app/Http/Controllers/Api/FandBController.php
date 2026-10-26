<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FandBMaster;

class FandBController extends Controller
{
    public function list()
{
    $data = FandBMaster::select('id', 'name', 'price', 'status', 'food_image')
        ->orderBy('id', 'asc')
        ->get()
        ->map(function ($item) {
            $item->food_image = $item->food_image
                ? url($item->food_image)
                : null;
            return $item;
        });

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    public function updateImage(Request $request, $id)
{
    $request->validate([
        'food_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $item = FandBMaster::find($id);

    if (!$item) {
        return response()->json([
            'success' => false,
            'message' => 'Item not found',
        ], 404);
    }

    // 🖼️ Upload image to public/storage/foodimage
    $image = $request->file('food_image');
    $filename = time() . '_' . $image->getClientOriginalName();

    // Move image
    $image->move(public_path('storage/foodimage'), $filename);

    // Save DB path
    $item->update([
        'food_image' => 'storage/foodimage/' . $filename,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'data' => [
            'id' => $item->id,
            'name' => $item->name,
            'image_url' => url('storage/foodimage/' . $filename),
        ],
    ]);
}

}