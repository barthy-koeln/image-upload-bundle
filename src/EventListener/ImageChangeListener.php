<?php


namespace Barthy\ImageUploadBundle\EventListener;


use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\Image;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImageChangeListener
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var ImageUploadConfig
     */
    private $imageUploadConfig;

    public function __construct(CacheManager $cacheManager, ImageUploadConfig $imageUploadConfig)
    {

        $this->cacheManager = $cacheManager;
        $this->imageUploadConfig = $imageUploadConfig;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $image = $args->getObject();

        if (!$image instanceof Image) {
            return;
        }

        $path = $this->imageUploadConfig->getImagePathPrefix().DIRECTORY_SEPARATOR.$image->getFileName();
        $this->cacheManager->remove($path);
    }

}
