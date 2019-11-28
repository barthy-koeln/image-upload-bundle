<?php


namespace Barthy\ImageUploadBundle\Tests\Entity;

use Barthy\ImageUploadBundle\Entity\ImageInterface;
use Barthy\ImageUploadBundle\Entity\ImageTrait;
use Barthy\ImageUploadBundle\Entity\TitleFileNameTrait;
use Barthy\ImageUploadBundle\Entity\TranslatedImageTrait;
use Barthy\ImageUploadBundle\Validator\FileTitleConstraint;
use Barthy\SlugFilenameBundle\Entity\SlugFileNameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @FileTitleConstraint()
 * @ORM\Entity()
 * @package Barthy\ImageUploadBundle\Entity
 * @Vich\Uploadable
 */
class SpecificImage extends AbstractTranslatable implements SlugFileNameInterface, ImageInterface
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
     * @Prezent\Translations(targetEntity="\Barthy\ImageUploadBundle\Tests\Entity\SpecificImageTranslation")
     */
    protected $translations;

    use ImageTrait;
    use TranslatedImageTrait;
    use TitleFileNameTrait;

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
