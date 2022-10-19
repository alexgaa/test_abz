<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tinify;

class ImageHelper
{
   const DEFAULT_SETTINGS = [
                "method" => "cover",
                "width" => 70,
                "height" => 70
            ];

    /**
     * @param Request $request
     * @param string $imageFormKey
     * @param string $pathSaveFile
     * @param array $settings
     * @return string
     */
    public function optimisation(
        Request $request,
        string $imageFormKey,
        string $pathSaveFile,
        array $settings = self::DEFAULT_SETTINGS): string
    {
        $pathFile = $request->file($imageFormKey)->store($pathSaveFile);
        try {
            Tinify\setKey(env('API_KEY_TINIFY'));
            $source = Tinify\fromFile($pathFile);
            $resized = $source->resize($settings);
            $resized->toFile($pathFile);
        } catch (Exception){
            Storage::delete($pathFile);
            $pathFile = '';
        }
        return $pathFile;
    }
}
