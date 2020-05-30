<?php


namespace Barthy\ImageUploadBundle;


use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\ImageInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImageListener
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var ImageUploadConfig
     */
    private $imageUploadConfig;

    public function __construct(
        CacheManager $cacheManager,
        ImageUploadConfig $imageUploadConfig
    ) {
        $this->cacheManager = $cacheManager;
        $this->imageUploadConfig = $imageUploadConfig;
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $unitOfWork = $eventArgs->getEntityManager()->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();

        foreach ($updatedEntities as $entity) {
            if (!$entity instanceof ImageInterface) {
                continue;
            }

            $path = $this->imageUploadConfig->getImagePathPrefix().DIRECTORY_SEPARATOR.$entity->getFileName();
            $this->cacheManager->remove($path);
        }
    }
}
