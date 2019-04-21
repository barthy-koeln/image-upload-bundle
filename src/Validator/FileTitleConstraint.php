<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 30.01.19
 * Time: 17:45
 */

namespace Barthy\ImageUploadBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * Class FileTitleConstraint
 * @package Barthy\ImageUploadBundle\Validator
 */
class FileTitleConstraint extends Constraint
{

    public $message = 'file_title_constraint.message';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return FileTitleConstraintValidator::class;
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
