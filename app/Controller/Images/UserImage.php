<?php

namespace App\Controller\Images;


use App\Utils\Image;
use App\Core\Response;

class UserImage {

    public static function getImage($imageName) {

        $relativeImagePath = "users/$imageName";
        
        [$imageContent, $imageMimeType] = Image::getImage($relativeImagePath);

        return new Response(200, $imageContent, $imageMimeType);
    }
}



