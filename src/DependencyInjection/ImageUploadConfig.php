<?php

namespace BarthyKoeln\ImageUploadBundle\DependencyInjection;

class ImageUploadConfig
{
    /**
     * @var string
     */
    private $fileNameLanguage;

    /**
     * @var string
     */
    private $maxFileSize;

    /**
     * @var string
     */
    private $imageClass;

    /**
     * @var string
     */
    private $imagePathPrefix;

    public function __construct(array $config)
    {
        $this->fileNameLanguage = $config['file_name_language'];
        $this->maxFileSize      = $config['max_file_size'];
        $this->imageClass       = $config['image_class'];
        $this->imagePathPrefix  = $config['image_path_prefix'];
    }

    public function getFileNameLanguage(): string
    {
        return $this->fileNameLanguage;
    }

    public function getMaxFileSize(): string
    {
        return $this->maxFileSize;
    }

    public function getImageClass(): string
    {
        return $this->imageClass;
    }

    public function getImagePathPrefix(): string
    {
        return $this->imagePathPrefix;
    }
}
