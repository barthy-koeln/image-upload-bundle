<?php

namespace BarthyKoeln\ImageUploadBundle\Validator;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class FileSizeConstraintValidator extends Assert\FileValidator
{
    private ImageUploadConfig $config;

    public function __construct(ImageUploadConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param \File      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof FileSizeConstraint) {
            $maxSize             = $this->config->getMaxFileSize();
            $constraint->maxSize = $maxSize;
        }

        parent::validate($value, $constraint);
    }
}
