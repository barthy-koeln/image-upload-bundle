<?php

namespace Tests\Entity;

use BarthyKoeln\ImageUploadBundle\Entity\ImageInterface;
use BarthyKoeln\ImageUploadBundle\Entity\ImageTrait;
use BarthyKoeln\ImageUploadBundle\Entity\TranslatedImageTrait;
use BarthyKoeln\ImageUploadBundle\Validator\FileTitleConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @FileTitleConstraint()
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class SpecificImage extends AbstractTranslatable implements ImageInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @Prezent\Translations(targetEntity="Tests\Entity\SpecificImageTranslation")
     */
    protected $translations;

    use ImageTrait;
    use TranslatedImageTrait;

    use TimestampableEntity;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
}
