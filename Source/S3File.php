<?php

namespace Gregwar\Image\Source;

use Gregwar\Image\Image;

/**
 * Open an image from a S3 storage
 */
class S3File extends Source
{
    protected $s3file;

    public function __construct($file)
    {
        $this->s3file = $file;
    }

    public function getFile()
    {
        return $this->s3file->signedUrl();
    }

    public function correct()
    {
        return false !== @exif_imagetype($this->s3file->signedUrl());
    }

    public function guessType()
    {
        if (function_exists('exif_imagetype')) {
            $type = @exif_imagetype($this->s3file->signedUrl());

            if (false !== $type) {
                if ($type == IMAGETYPE_JPEG) {
                    return 'jpeg';
                }

                if ($type == IMAGETYPE_GIF) {
                    return 'gif';
                }

                if ($type == IMAGETYPE_PNG) {
                    return 'png';
                }
            }
        }

        $parts = explode('.', $this->s3file->s3key);
        $ext = strtolower($parts[count($parts)-1]);

        if (isset(Image::$types[$ext])) {
            return Image::$types[$ext];
        }

        return 'jpeg';
    }

    public function getInfos()
    {
        return $this->s3file->etag;
        // return $this->s3file->signedUrl();
    }

    public function getEtag() {
        $etag = $this->s3file->etag;
        $etag = str_replace('"', '', $etag);
        return $etag;
    }
}
