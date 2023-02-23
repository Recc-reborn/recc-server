<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Artist;
use App\Models\Track;

class UserController extends Controller
{
    /**
     * Returns current user
     */
    public function get()
    {
        if (Auth::check()) {
            return Auth::user();
        }
        return response('Unauthenticated', 401);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "email" => "email|required",
            "name" => "required",
            "password" => "required"
        ]);

        $userInfo = array_merge($request->all(), ['role' => 'user', 'password' => Hash::make($request->password)]);

        $isEmailTaken = User::where(['email' => $request->email])->exists();

        if ($isEmailTaken) {
            throw ValidationException::withMessages([
                'email' => ['Ya existe un usuario con este correo electrónico'],
            ]);
        }

        return User::create($userInfo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password ? $request->password : $user->password;

        $user->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    /**
     * Add preferred artists for this user
     */
    public function addPreferredArtists(Request $request)
    {
        $user = $request->user();

        try {
            // $request->all() should return a list of artist IDs
            $user->addPreferredArtists($request->all());
        } catch (QueryException) {
            abort(422, 'One or more provided parameters are not correct');
        }
    }

    /**
     * Get this user's preferred artists' IDs
     */
    public function getPreferredArtists(Request $request)
    {
        $user = $request->user();
        return response()->json(
            $user->preferredArtists()
                ->get(['id'])->pluck(['id'])
        );
    }

    /**
     * Remove preferred artists for this user
     */
    public function removePreferredArtists(Request $request)
    {
        $user = $request->user();

        // $request->all() should return a list of last.fm artist URLs
        $artistIdsToRemove = Artist::whereIn(
            'id',
            $request->all()
        )->get('id')->pluck('id')->toArray();

        try {
            $user->removePreferredArtists($artistIdsToRemove);
        } catch (QueryException) {
            abort(422, 'One or more provided parameters are not correct');
        }
    }

    /**
     * Add preferred tracks for this user
     */
    public function addPreferredTracks(Request $request)
    {
        $user = $request->user();

        try {
            // $request->all() should return a list of artist IDs
            $user->addPreferredTracks($request->all());
        } catch (QueryException) {
            abort(422, 'One or more provided parameters are not correct');
        }
    }

    /**
     * Get this user's preferred tracks IDs
     */
    public function getPreferredTracks(Request $request)
    {
        $user = $request->user();
        return response()->json(
            $user->preferredTracks()
                ->get(['id'])->pluck(['id'])
        );
    }

    /**
     * Remove preferred tracks for this user
     */
    public function removePreferredTracks(Request $request)
    {
        $user = $request->user();

        // $request->all() should return a list of last.fm artist URLs
        $TrackIdsToRemove = Track::whereIn(
            'id',
            $request->all()
        )->get('id')->pluck('id')->toArray();

        try {
            $user->removePreferredTracks($TrackIdsToRemove);
        } catch (QueryException) {
            abort(422, 'One or more provided parameters are not correct');
        }
    }
}
