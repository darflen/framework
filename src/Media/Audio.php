<?php

declare(strict_types=1);

namespace Darflen\Framework\Media;

use Darflen\Framework\Config\Config;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Media\Audio as MediaAudio;

class Audio
{
    private string $path;

    private MediaAudio $audio;

    private Format $metadata;

    private Mp3 $encoder;

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
        $this->audio = $ffmpeg->open($path);
        $this->metadata = $ffprobe->format($path);
        $this->encoder = new Mp3();
    }

    public function getDuration(): int
    {
        return (int) $this->metadata->get('duration');
    }

    public function compress(int $percentage): self
    {
        $kiloBitrate = round($this->metadata->get("bit_rate") * ($percentage / 100) / 1000);
        $this->encoder->setAudioKiloBitrate($kiloBitrate);
        return $this;
    }

    public function clip(int $start, int $duration): self
    {
        $this->audio->filters()->clip(
            TimeCode::fromSeconds($start),
            TimeCode::fromSeconds($duration),
        );
        return $this;
    }

    public function save(string $path = ''): void
    {
        if ($path === '') {
            $pathInfo = pathinfo($this->path);
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            $path = normalizePath(sys_get_temp_dir() . '/' . uniqid(more_entropy: true) . $extension);
            $this->audio->save($this->encoder, $path);
            if (file_exists($path)) {
                rename($path, $this->path);
            }
            return;
        }
        $path = normalizePath($path);
        $this->audio->save($this->encoder, $path);
    }
}
