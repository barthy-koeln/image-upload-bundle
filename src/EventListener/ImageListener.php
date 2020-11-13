<?php

namespace BarthyKoeln\ImageUploadBundle\EventListener;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Entity\ImageInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImageListener
{
    private CacheManager $cacheManager;

    private ImageUploadConfig $imageUploadConfig;

    public function __construct(
        CacheManager $cacheManager,
        ImageUploadConfig $imageUploadConfig
    ) {
        $this->cacheManager      = $cacheManager;
        $this->imageUploadConfig = $imageUploadConfig;
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $unitOfWork      = $eventArgs->getEntityManager()->getUnitOfWork();
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
