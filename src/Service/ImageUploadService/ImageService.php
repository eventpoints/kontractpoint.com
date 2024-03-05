<?php

declare(strict_types=1);

namespace App\Service\ImageUploadService;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageService
{
    public function processAvatar(UploadedFile $file): EncodedImageInterface
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        $image->cover(400, 400);
        return $image->toPng();
    }

    public function processPhoto(UploadedFile $file): EncodedImageInterface
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        $image->resize(800, 800, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image->toPng();
    }
}
