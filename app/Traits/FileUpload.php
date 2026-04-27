<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    // public static function imageUpload($file,String $path){

    //     $image  = time() . '.' . $file->getClientOriginalExtension();
    //     $path = $path . '/' . $image;
    //     Storage::disk('public')->put($path, file_get_contents($file));
    //    return  $image;
    // }

    public static function imageUpload($file, String $path)
    {
        $imageName = time() . '.' . $file->getClientOriginalExtension();
        $directory = public_path($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $file->move($directory, $imageName);
        return $imageName;
    }
}