<?php

namespace BarthyKoeln\ImageUploadBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class FileSizeConstraint extends Assert\File
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return FileSizeConstraintValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
