<?php

namespace Resizer\ImageResizer;

use Eventviva\ImageResize;
use Eventviva\ImageResizeException;

class ImageResizer
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
        var_dump($filename);

        try {
            $image = new ImageResize($this->sourcePath.'/'.$filename);

            $ratio = $image->getSourceHeight() <=> $image->getSourceWidth();

            switch ($ratio) {
                case 1:
                    $image->resizeToHeight(640);
                    break;
                case -1:
                    $image->resizeToWidth(640);
                    break;
                case 0: //default
                default:
                    $image->resize(640, 640);
                    break;
            }

            $path = $this->getSavePath($filename);
            $image->save($path, IMAGETYPE_JPEG);
        } catch (ImageResizeException $e) {
            throw $e;
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getSavePath(string $filename): string
    {
        $filenameArray = explode('.', $filename);
        $name = $filenameArray[0];

        $path = $this->sourcePath.'/'.$name.'.jpg';

        return $path;
    }
}