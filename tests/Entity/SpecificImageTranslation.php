<?php


namespace Barthy\ImageUploadBundle\Tests\Entity;


use Barthy\ImageUploadBundle\Entity\ImageTranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * Class SpecificImageTranslation
 *
 * @ORM\Entity()
 *
 * @package Barthy\ImageUploadBundle\Tests\Entity
 */
class SpecificImageTranslation extends AbstractTranslation
{

    use ImageTranslationTrait;

    /**
     * @var \Barthy\ImageUploadBundle\Tests\Entity\SpecificImage
     * @Prezent\Translatable(targetEntity="\Barthy\ImageUploadBundle\Tests\Entity\SpecificImage")
     */
    protected $translatable;
}
