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
        return Artist::paginate();
    }
}
