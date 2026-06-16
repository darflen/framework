<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Override;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    private StreamInterface $fileStream;

    private int $size;

    private int $error;

    private const AVAILABLE_ERRORS = [
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
    ];

    private ?string $clientName = null;

    private ?string $clientMediaType = null;

    private bool $isMoved = false;

    public function __construct(?StreamInterface $fileStream, int $size, int $error, ?string $clientName = null, ?string $clientMediaType = null)
    {
        $this->validateError($error);
        $this->fileStream = $fileStream;
        $this->size = $size;
        $this->error = $error;
        $this->clientName = $clientName;
        $this->clientMediaType = $clientMediaType;
    }

    #[Override]
    public function getStream(): StreamInterface
    {
        $this->validateGood();
        return $this->fileStream;
    }

    #[Override]
    public function moveTo(string $targetPath): void
    {
        $this->validateGood();
        $uri = $this->fileStream->getMetadata('uri');
        if (is_uploaded_file($uri)) {
            $this->isMoved = @move_uploaded_file($uri, $targetPath);
        } else {
            $this->isMoved = @rename($uri, $targetPath);
        }
        if (!$this->isMoved) {
            throw new RuntimeException('Move failed');
        }
        $this->isMoved = true;
    }

    #[Override]
    public function getSize(): ?int
    {
        return $this->size;
    }

    #[Override]
    public function getError(): int
    {
        return $this->error;
    }

    #[Override]
    public function getClientFilename(): ?string
    {
        return $this->clientName;
    }

    #[Override]
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    private function validateGood(): void
    {
        if ($this->error !== \UPLOAD_ERR_OK) {
            throw new RuntimeException('File has an error');
        }
        if ($this->isMoved) {
            throw new RuntimeException('File is already moved');
        }
    }

    private function validateError(int $error): void
    {
        if (!key_exists($error, self::AVAILABLE_ERRORS)) {
            throw new InvalidArgumentException('Invalid error code');
        }
    }
}
