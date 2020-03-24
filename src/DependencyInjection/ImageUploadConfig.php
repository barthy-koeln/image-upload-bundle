<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 11.07.18
 * Time: 17:27
 */

namespace Barthy\ImageUploadBundle\DependencyInjection;


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
        $this->maxFileSize = $config['max_file_size'];
        $this->imageClass = $config['image_class'];
        $this->imagePathPrefix = $config['image_path_prefix'];
    }

    /**
     * @return string
     */
    public function getFileNameLanguage(): string
    {
        return $this->fileNameLanguage;
    }

    /**
     * @return string
     */
    public function getMaxFileSize(): string
    {
        return $this->maxFileSize;
    }

    /**
     * @return string
     */
    public function getImageClass(): string
    {
        return $this->imageClass;
    }

    /**
     * @return string
     */
    public function getImagePathPrefix(): string
    {
        return $this->imagePathPrefix;
    }
}
