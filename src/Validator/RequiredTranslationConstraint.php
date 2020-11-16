<?php

namespace BarthyKoeln\ImageUploadBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class RequiredTranslationConstraint extends Constraint
{
    public string $message = 'file_title_constraint.message';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return RequiredTranslationConstraintValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
