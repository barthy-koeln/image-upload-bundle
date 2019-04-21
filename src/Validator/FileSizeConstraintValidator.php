<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 30.01.19
 * Time: 18:02
 */

namespace Barthy\ImageUploadBundle\Validator;


use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\Image;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class FileSizeConstraintValidator extends Assert\FileValidator
{

    /**
     * @var ImageUploadConfig
     */
    private $config;

    public function __construct(ImageUploadConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param Image      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof FileSizeConstraint) {
            $maxSize = $this->config->getMaxFileSize();
            $constraint->maxSize = $maxSize;
        }

        parent::validate($value, $constraint);
    }
}
