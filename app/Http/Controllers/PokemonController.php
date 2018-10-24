<?php

namespace App\Http\Controllers;

use App\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{

    public function search(Request $request)
    {
        $matches = Pokemon::where('name', 'like', '%' . $request->keyword . '%')
                            ->orWhere('types', 'like', '%' . $request->keyword . '%')
                            ->get();

        return response()->json($matches);
    }
}
