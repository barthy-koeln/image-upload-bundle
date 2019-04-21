<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 30.01.19
 * Time: 18:00
 */

namespace Barthy\ImageUploadBundle\Validator;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * Class FileSizeConstraint
 * @package Barthy\ImageUploadBundle\Validator
 */
class FileSizeConstraint extends Assert\File
{

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return FileSizeConstraintValidator::class;
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
