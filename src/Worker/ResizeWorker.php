<?php

$gmworker = new GearmanWorker();
$gmworker->addServer();
$gmworker->addFunction("resize", "resize");

while ($gmworker->work()) {
    if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
        echo "return_code: ".$gmworker->returnCode()."\n";
        break;
    }
}

function resize(GearmanJob $job)
{
    $filename = $job->workload();
    $destPath = getSavePath($filename);

    $size = getimagesize($filename);
    $src = createImage($filename);

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

    $client = new GearmanClient();
    $client->addServer();
    $client->doBackground('upload', $destPath);
}

function createImage($file)
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

function getSavePath(string $filename): string
{
    $basename = basename($filename);
    $filenameArray = explode('.', $basename);
    $name = $filenameArray[0];

    $path = __DIR__.'/../../images_resized/'.$name.'.jpg';

    return $path;
}
