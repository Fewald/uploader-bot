<?php

namespace Resizer\ImageResizer;

class GDImageResizer
{
    /** @var  string */
    protected $sourcePath;

    /** @var  string */
    protected $destPath;

    /**
     * ImageResizer constructor.
     * @param string $sourcePath
     * @param string $destPath
     */
    public function __construct(string $sourcePath, string $destPath)
    {
        $this->sourcePath = $sourcePath;
        $this->destPath = $destPath;

    }


    public function resize(string $filename)
    {
        $sourcePath = $this->sourcePath.'/'.$filename;
        $destPath = $this->getSavePath($filename);

        $size = getimagesize($sourcePath);
        $image = $this->createImage($sourcePath);

        $this->resizeImage($size, $image, $destPath);
    }

    /**
     * @param $file
     * @return resource
     * @throws \Exception
     */
    protected function createImage($file)
    {
        $extension = strtolower(strrchr($file, '.'));

        switch ($extension) {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $img = @imagecreatefromgif($file);
                break;
            case '.png':
                $img = @imagecreatefrompng($file);
                break;
            default:
                throw new \Exception("Extension is not supported: $file``");
                break;
        }

        return $img;
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getSavePath(string $filename): string
    {
        $filenameArray = explode('.', $filename);
        $name = $filenameArray[0];

        $path = $this->destPath.'/'.$name.'.jpg';

        return $path;
    }

    protected function resizeImage($size, $src, $destPath)
    {
        $width = $oldWidth = $size[0];
        $height = $oldHeight = $size[1];
        $ratio = $oldWidth / $oldHeight;
        $isBigger = max($oldHeight, $oldWidth) > 640;
        if ($isBigger) {
            if ($ratio > 1) {
                $width = 640;
                $height = 640 / $ratio;
            } else {
                $width = 640 * $ratio;
                $height = 640;
            }
        }


        $dst = imagecreatetruecolor(640, 640);

        $clear = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $clear);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
        imagejpeg($dst, $destPath, 100);

        // free resources
        imagedestroy($src);
        imagedestroy($dst);
    }
}