<?php

namespace App\Controller\Images;


use App\Utils\Image;
use App\Core\Response;

class NewsImage {

    public static function getImage($imageName) {

        $relativeImagePath = "news/$imageName";
        
        [$imageContent, $imageMimeType] = Image::getImage($relativeImagePath);

        return new Response(200, $imageContent, $imageMimeType);
    }
}



