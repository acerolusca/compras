<?php

namespace App\Controller\Images;


use App\Utils\Image;
use App\Core\Response;

class EditorImage {

    public static function getImage($relativeImagePath) {

        $relativeImagePath = "editor/$relativeImagePath";
        
        [$imageContent, $imageMimeType] = Image::getImage($relativeImagePath);

        return new Response(200, $imageContent, $imageMimeType);
    }
}



