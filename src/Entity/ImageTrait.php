<?php

namespace BarthyKoeln\ImageUploadBundle\Entity;

use BarthyKoeln\ImageUploadBundle\Validator\FileSizeConstraint;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait ImageTrait
{
    /**
     * Mapping provided by implementation.
     *
     * @var int|string
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $fileName = null;

    /**
     * @FileSizeConstraint()
     * @Vich\UploadableField(
     *     mapping="images",
     *     fileNameProperty="fileName",
     *     size="size",
     *     mimeType="mimeType",
     *     dimensions="dimensions"
     * )
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $size = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $mimeType = null;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private ?int $position = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $x = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $y = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $w = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $h = null;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $dimensions = null;

    public function __toString()
    {
        return $this->getFileName() ?? '';
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @Assert\Callback
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
     * @throws Exception
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (null !== $image) {
            $this->setUpdatedAt(new DateTime());
        }
    }

    public function getJSONCropData(): string
    {
        if (null === $this->getX() || null === $this->getY() || null === $this->getW() || null === $this->getH()) {
            return '';
        }

        return json_encode(
            [
                'x'        => $this->getX(),
                'y'        => $this->getY(),
                'width'    => $this->getW(),
                'height'   => $this->getH(),
                'original' => $this->getDimensions(),
            ]
        );
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): void
    {
        $this->x = $x;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): void
    {
        $this->y = $y;
    }

    public function getW(): ?int
    {
        return $this->w;
    }

    public function setW(?int $w): void
    {
        $this->w = $w;
    }

    public function getH(): ?int
    {
        return $this->h;
    }

    public function setH(?int $h): void
    {
        $this->h = $h;
    }

    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    public function setDimensions(?array $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
