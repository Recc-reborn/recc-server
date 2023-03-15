<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("playlists", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("title")->nullable();
            $table->string("origin");
            $table->foreignId("user_id")->nullable()->constrained('users')->nullOnDelete();
        });

        // playlist<->track many-to-many relationship
        Schema::create("playlist_track", function (Blueprint $table) {
            // describes the order of the tracks in the playlist
            $table->float("ordinal")->default(0);
            $table->foreignId("playlist_id")->constrained('playlists')->cascadeOnDelete();
            $table->foreignId("track_id")->constrained('tracks')->cascadeOnDelete();
        });

        // User <-> Playlist many to many relationship
        Schema::create('user_playlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("playlists");
        Schema::dropIfExists("playlist_track");
        Schema::dropIfExists("user_playlist");
    }
};
