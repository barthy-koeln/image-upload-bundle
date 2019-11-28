<?php


namespace Barthy\ImageUploadBundle\Entity;


use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageInterface
{

    /**
     * @return File
     */
    public function getImageFile(): ?File;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $image
     */
    public function setImageFile(?File $image = null): void;

    public function __toString();

    /**
     * @return string
     */
    public function getFileName(): ?string;

    /**
     * @param string $fileName
     */
    public function setFileName(?string $fileName): void;

    /**
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt);

    /**
     * @return int
     */
    public function getSize(): ?int;

    /**
     * @param int $size
     */
    public function setSize(?int $size): void;

    /**
     * @return string
     */
    public function getMimeType(): ?string;

    /**
     * @param string $mimeType
     */
    public function setMimeType(?string $mimeType): void;

    /**
     * @return int|null
     */
    public function getX(): ?int;

    /**
     * @param int|null $x
     */
    public function setX(?int $x): void;

    /**
     * @return int|null
     */
    public function getY(): ?int;

    /**
     * @param int|null $y
     */
    public function setY(?int $y): void;

    /**
     * @return int|null
     */
    public function getW(): ?int;

    /**
     * @param int|null $w
     */
    public function setW(?int $w): void;

    /**
     * @return int|null
     */
    public function getH(): ?int;

    /**
     * @param int|null $h
     */
    public function setH(?int $h): void;

    /**
     * @return string
     */
    public function getJSONCropData(): string;

    /**
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;

    /**
     * @param array|null $dimensions
     */
    public function setDimensions(?array $dimensions);

    /**
     * @return array|null
     */
    public function getDimensions(): ?array;
}
