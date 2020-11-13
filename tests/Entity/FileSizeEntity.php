<?php

namespace Tests\Entity;

use BarthyKoeln\ImageUploadBundle\Validator\FileSizeConstraint;
use Symfony\Component\HttpFoundation\File\File;

class FileSizeEntity
{
    /**
     * @FileSizeConstraint()
     */
    public File $imageFile;
}
