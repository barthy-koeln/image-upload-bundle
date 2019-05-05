<?php

namespace Barthy\ImageUploadBundle\Entity;

use Barthy\CachedPrezentTranslation\CachedPrezentTranslationTrait;
use Barthy\ImageUploadBundle\Validator\FileSizeConstraint;
use Barthy\ImageUploadBundle\Validator\FileTitleConstraint;
use Barthy\SlugFilenameBundle\Entity\SlugFileNameInterface;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @FileTitleConstraint()
 * @ORM\Entity
 * @package Barthy\ImageUploadBundle\Entity
 * @Vich\Uploadable
 */
class Image extends AbstractTranslatable implements SlugFileNameInterface
{

    use TimestampableEntity;
    use CachedPrezentTranslationTrait;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Prezent\Translations(targetEntity="Barthy\ImageUploadBundle\Entity\ImageTranslation")
     * @Assert\Valid
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $fileName;

    /**
     * @FileSizeConstraint()
     * @Vich\UploadableField(mapping="images", fileNameProperty="fileName", size="size", mimeType="mimeType", dimensions="dimensions")
     * @var File
     */
    private $imageFile;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @var integer
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $x;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $y;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $w;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $h;
    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    private $dimensions;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $image
     *
     * @throws Exception
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new DateTime());
        }
    }

    public function __toString()
    {
        return $this->getFileName() ?? '';
    }

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(?string $locale = null): ?string
    {
        /**
         * @var ImageTranslation $trans
         */
        $trans = $this->translate($locale);

        return $trans->getTitle();
    }

    public function getAlt(?string $locale = null): ?string
    {
        /**
         * @var ImageTranslation $trans
         */
        $trans = $this->translate($locale);

        return $trans->getAlt();
    }

    /**
     * @return AbstractTranslation
     */
    function createNewTranslation()
    {
        return new ImageTranslation();
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (empty($this->getImageFile()) && empty($this->getFileName())) {
            $context
                ->buildViolation('image.file.not_empty')
                ->setTranslationDomain('barthy_image_upload')
                ->atPath('imageFile')
                ->addViolation();
        }
    }

    /**
     * @return int|null
     */
    public function getX(): ?int
    {
        return $this->x;
    }

    /**
     * @param int|null $x
     */
    public function setX(?int $x): void
    {
        $this->x = $x;
    }

    /**
     * @return int|null
     */
    public function getY(): ?int
    {
        return $this->y;
    }

    /**
     * @param int|null $y
     */
    public function setY(?int $y): void
    {
        $this->y = $y;
    }

    /**
     * @return int|null
     */
    public function getW(): ?int
    {
        return $this->w;
    }

    /**
     * @param int|null $w
     */
    public function setW(?int $w): void
    {
        $this->w = $w;
    }

    /**
     * @return int|null
     */
    public function getH(): ?int
    {
        return $this->h;
    }

    /**
     * @param int|null $h
     */
    public function setH(?int $h): void
    {
        $this->h = $h;
    }

    /**
     * @return string
     */
    public function getJSONCropData(): string
    {
        if ($this->getX() === null || $this->getY() === null || $this->getW() === null || $this->getH() === null) {
            return '';
        } else {
            return json_encode(
                [
                    "x"        => $this->getX(),
                    "y"        => $this->getY(),
                    "width"    => $this->getW(),
                    "height"   => $this->getH(),
                    "original" => $this->getDimensions(),
                ]
            );
        }
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @param array|null $dimensions
     */
    public function setDimensions(?array $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return array|null
     */
    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    public function getSlugFieldValue(string $locale): ?string
    {
        $this->setCurrentLocale($locale);

        return $this->getTitle();
    }

    public function getSlugFieldName(): string
    {
        return 'title';
    }

    public function getSlug(string $locale): ?string
    {
        $this->setCurrentLocale($locale);
        $slug = mb_strtolower($this->getTitle());
        $slugify = new Slugify();
        $slug = $slugify->slugify($slug);

        return $slug;
    }
}
