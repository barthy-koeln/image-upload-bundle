<?php

namespace BarthyKoeln\ImageUploadBundle\DependencyInjection;

class ImageUploadConfig
{
    private string $maxFileSize;

    private string $imageClass;

    private string $imagePathPrefix;

    private string $requiredTranslation;

    public function __construct(array $config)
    {
        $this->requiredTranslation = $config['required_translation'];
        $this->maxFileSize         = $config['max_file_size'];
        $this->imageClass          = $config['image_class'];
        $this->imagePathPrefix     = $config['image_path_prefix'];
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

    public function getRequiredTranslation(): ?string
    {
        return $this->requiredTranslation;
    }
}
