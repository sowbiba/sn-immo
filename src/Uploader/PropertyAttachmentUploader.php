<?php

namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PropertyAttachmentUploader
{
    /**
     * @var string
     */
    private $attachmentsDirectory;

    public function __construct(string $attachmentsDirectory)
    {
        $this->attachmentsDirectory = $attachmentsDirectory;
    }

    public function upload(string $attachmentType, UploadedFile $file): string
    {
        $targetDirectory = rtrim($this->attachmentsDirectory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . $attachmentType;

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($targetDirectory, $fileName);

        return $fileName;
    }
}
