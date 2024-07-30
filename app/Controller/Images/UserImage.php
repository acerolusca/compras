<?php

namespace App\Controller\Images;


use App\Utils\Image;
use App\Core\Response;

class UserImage {

    public static function getImage($relativeImagePath) {

        $relativeImagePath = "users/$relativeImagePath";
        
        [$imageContent, $imageMimeType] = Image::getImage($relativeImagePath);

        return new Response(200, $imageContent, $imageMimeType);
    }
}



