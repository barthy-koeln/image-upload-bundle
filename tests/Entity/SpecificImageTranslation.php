<?php

namespace Tests\Entity;

use BarthyKoeln\ImageUploadBundle\Entity\ImageTranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Entity()
 */
class SpecificImageTranslation extends AbstractTranslation
{
    use ImageTranslationTrait;

    /**
     * @var \Tests\Entity\SpecificImage
     * @Prezent\Translatable(targetEntity="Tests\Entity\SpecificImage")
     */
    protected $translatable;
}
