<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    /**
     * Show all artists
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input("search");
        $query = $search ? Artist::search($search) : Artist::query();

        $perPage = $request->input("per_page", 15);

        return $query->paginate($perPage);
    }
}
