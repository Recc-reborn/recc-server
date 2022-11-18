<?php
namespace App\Enums;

abstract class PlaylistOrigin
{
    // Recommended using favorite artists
    public const RECOMMENDED_FAVORITES = "RECOMMENDED_FAVORITES";
    public const RECOMMENDED_HABITS = "RECOMMENDED_HABITS";
    public const RECOMMENDED_POPULAR = "RECOMMENDED_POPULAR";
    // Created by a user
    public const CUSTOM = "CUSTOM";
}
