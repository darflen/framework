<?php

declare(strict_types=1);

namespace Darflen\Framework\Media;

use Darflen\Framework\Config\Config;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Media\Video as MediaVideo;
use InvalidArgumentException;

class Video
{
    private string $path;

    private MediaVideo $container;

    private Format $metadata;

    private X264 $encoder;

    public function __construct(string $path, Config $config)
    {
        $this->path = normalizePath($path);
        $ffmpegParams = [
            "ffmpeg.binaries" => $config->get('media.ffmpeg.binaries.processor'),
            "ffprobe.binaries" => $config->get('media.ffmpeg.binaries.probe'),
            "timeout" => $config->get('media.ffmpeg.timeout'),
            "ffmpeg.threads" => $config->get('media.ffmpeg.threads')
        ];
        $ffmpeg = FFMpeg::create($ffmpegParams);
        $ffprobe = FFProbe::create($ffmpegParams);
        $container = $ffmpeg->open($path);
        if (!is_a($container, MediaVideo::class)) {
            throw new InvalidArgumentException('File format must be a video format');
        }
        $this->container = $container;
        $this->metadata = $ffprobe->format($path);
        $this->encoder = new X264();
        $this->encoder->setInitialParameters($config->get('media.ffmpeg.flags', []));
    }

    public function getDuration(): int
    {
        return (int) $this->metadata->get('duration');
    }

    public function getSize(): array
    {
        $videoStream = $this->container->getStreams()->videos()->first();
        $videoDimensions = $videoStream->getDimensions();
        $width = $videoDimensions->getWidth();
        $height = $videoDimensions->getHeight();
        return ['width' => $width, 'height' => $height];
    }

    public function getWidth(): int
    {
        $dimensions = $this->getSize();
        return $dimensions['width'];
    }

    public function getHeight(): int
    {
        $dimensions = $this->getSize();
        return $dimensions['height'];
    }

    public function compress(int $percentage): self
    {
        $audioStream = $this->container->getStreams()->audios()->first();
        $audioBitrate = 22000;
        if (!is_null($audioStream)) {
            $audioBitrate = $audioStream->get('bit_rate', 0);
        }
        $kiloBitrate = max(round($this->metadata->get("bit_rate") * ($percentage / 100) / 1000), 5);
        $audioKiloBitrate = min(128, $audioBitrate);
        $this->encoder->setKiloBitrate($kiloBitrate);
        if ($audioBitrate > 0) {
            $this->encoder->setAudioKiloBitrate($audioKiloBitrate);
        }
        return $this;
    }

    public function scale(int $percentage): self
    {
        $videoStream = $this->container->getStreams()->videos()->first()->getDimensions();
        $width = (int) round($videoStream->getWidth() * ($percentage / 100));
        $height = (int) round($videoStream->getHeight() * ($percentage / 100));
        $this->container->filters()->resize(new Dimension($width, $height), ResizeFilter::RESIZEMODE_INSET);
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

    public function resize(int $width, int $height): self
    {
        $this->container->filters()->resize(new Dimension($width, $height), ResizeFilter::RESIZEMODE_INSET);
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

    public function save(string $path = ''): void
    {
        if ($path === '') {
            $pathInfo = pathinfo($this->path);
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            $path = normalizePath(sys_get_temp_dir() . '/' . uniqid(more_entropy: true) . $extension);
            $this->container->save($this->encoder, $path);
            if (file_exists($path)) {
                rename($path, $this->path);
            }
            return;
        }
        $path = normalizePath($path);
        $this->container->save($this->encoder, $path);
    }

    public function saveThumbnail(string $path, int $time = -1): void
    {
        $path = normalizePath($path);
        $time = $time === -1 ? (ceil($this->getDuration() / 2) + 1) : abs($time);
        $thumbnail = $this->container->frame(TimeCode::fromSeconds($time));
        $thumbnail->save($path);
    }
}
