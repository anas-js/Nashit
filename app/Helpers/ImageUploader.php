<?php

namespace App\Helpers;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploader
{
  // proces($image, [$x, $y, $width, $height], [$widthImage, $heightImage], $maxMBSize)
    static function proces($image, $cropData, $size,$aspectRatio, $mb)
    {
        $dim = $image->dimensions();
        $imageDimWidth = $dim[0];
        $imageDimHeight = $dim[1];

        $x = $cropData[0];
        $y = $cropData[1];
        $width = $cropData[2];
        $height = $cropData[3];

        if (($x > $imageDimWidth) || ($y > $imageDimHeight) || ($width > $imageDimWidth) || ($height > $imageDimHeight)) {
            throw ValidationException::withMessages([
                'image' => 'Error in image crop data'
            ]);
        }



        if ((($imageDimWidth - $x) < $width) || (($imageDimHeight - $y) < $height)) {
            throw ValidationException::withMessages([
                'image' => 'Error in image crop size data'
            ]);
        }
        //1.84
        //1.8350000
        //1.8450000

        //2.3
        //2.250000
        //2.350000
        $precision = explode('.',$aspectRatio);
        if(!isset($precision[1])) {
            $precision = 1;
        } else {
            $precision = strlen($precision[1]);
        }

        if ((round($width / $height, $precision)) != $aspectRatio) {
            throw ValidationException::withMessages([
                'image' => 'Error in image height and width'
            ]);
        }



        $editorImage = Image::read($image);
        $editorImage->crop($width, $height, $x, $y);
        $editorImage->resizeDown($size[0], $size[1]);

        $imageStore = $editorImage->toJpeg();
        $imageSize = $imageStore->size() / 1024 / 1024;



        $quality = 90;
        while (($quality >= 10) && ($imageSize > $mb)) {
            $imageStore = $editorImage->toJpeg($quality);
            $imageSize = $imageStore->size() / 1024 / 1024;
            $quality = $quality - 10;
        }

        if ($imageSize > $mb) {
            throw ValidationException::withMessages([
                'image' => "Error file size is more than $mb MB"
            ]);
        }

        return  $imageStore;
    }
}
