<?php

namespace BarthyKoeln\ImageUploadBundle\Entity;

use DateTime;
use Symfony\Component\HttpFoundation\File\File;

interface ImageInterface
{
    public function getImageFile(): ?File;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setImageFile(?File $image = null): void;

    public function __toString();

    public function getFileName(): ?string;

    public function setFileName(?string $fileName): void;

    public function getUpdatedAt();

    public function setUpdatedAt(DateTime $updatedAt);

    public function getSize(): ?int;

    public function setSize(?int $size): void;

    public function getMimeType(): ?string;

    public function setMimeType(?string $mimeType): void;

    public function getX(): ?int;

    public function setX(?int $x): void;

    public function getY(): ?int;

    public function setY(?int $y): void;

    public function getW(): ?int;

    public function setW(?int $w): void;

    public function getH(): ?int;

    public function setH(?int $h): void;

    public function getJSONCropData(): string;

    public function setPosition(?int $position): void;

    public function setDimensions(?array $dimensions): void;

    public function getDimensions(): ?array;
}
