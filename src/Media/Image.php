<?php

declare(strict_types=1);

namespace Darflen\Framework\Media;

use Imagick;

class Image
{
    private string $path;

    private Imagick $imagick;

    public function __construct(string $path)
    {
        $this->path = normalizePath($path);
        $this->imagick = new Imagick($this->path);
    }

    public function getSize(): array
    {
        return $this->imagick->getImageGeometry();
    }

    public function getWidth(): int
    {
        return $this->imagick->getImageWidth();
    }

    public function getHeight(): int
    {
        return $this->imagick->getImageHeight();
    }

    public function format(string $format): self
    {
        $this->imagick->setImageFormat($format);
        return $this;
    }

    public function compress(int $quality): self
    {
        $this->imagick->setCompressionQuality($quality);
        return $this;
    }

    public function scale(int $percentage): self
    {
        $size = $this->getSize();
        $width = (int) round($size['width'] * ($percentage / 100));
        $height = (int) round($size['height'] * ($percentage / 100));
        $this->imagick->scaleImage($width, $height);
        return $this;
    }

    public function scaleDown(int $percentage, int $minWidth = 0, int $minHeight = 0): self
    {
        $size = $this->getSize();
        if ($size['width'] >= $minWidth && $size['height'] >= $minHeight) {
            $this->scale($percentage);
        }
        return $this;
    }

    public function crop(int $width, int $height, int $offsetX = 0, int $offsetY = 0): self
    {
        $this->imagick->cropImage($width, $height, $offsetX, $offsetY);
        return $this;
    }

    public function resize(int $width, int $height): self
    {
        $this->imagick->scaleImage($width, $height);
        return $this;
    }

    public function resizeDown(int $width, int $height, int $minWidth = 0, int $minHeight = 0): self
    {
        $size = $this->getSize();
        if ($size['width'] >= $minWidth && $size['height'] >= $minHeight) {
            $this->resize($width, $height);
        }
        return $this;
    }

    public function rotate(int $degrees): self
    {
        $this->imagick->rotateImage($this->imagick->getImageBackgroundColor(), $degrees);
        return $this;
    }

    public function flip(string $direction): self
    {
        if ($direction === 'v') {
            $this->imagick->flipImage();
        } elseif ($direction === 'h') {
            $this->imagick->flopImage();
        }
        return $this;
    }

    public function orientate(): self
    {
        $this->imagick->autoOrient();
        return $this;
    }

    public function interlace(): self
    {
        $this->imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        return $this;
    }

    public function save(string $path = ''): void
    {
        $path = $path === '' ? $this->path : normalizePath($path);
        $this->imagick->writeImage($path);
        $this->imagick->clear();
    }
}
