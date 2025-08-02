<?php

namespace App\Services;

class MediaService {
    public static function uploadFile($fileName,$path,$file) {
        $file->storeAs("public/".$path, $fileName);
        return $fileName;
    }
}