<?php

namespace BarthyKoeln\ImageUploadBundle\Validator;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Entity\ImageTranslationTrait;
use Locale;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RequiredTranslationConstraintValidator extends ConstraintValidator
{
    private ImageUploadConfig $config;

    public function __construct(ImageUploadConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param \BarthyKoeln\ImageUploadBundle\Entity\ImageInterface $value      The value that should be validated
     * @param Constraint                                           $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!(method_exists($value, 'getTranslations'))) {
            return;
        }

        $locale = $this->config->getRequiredTranslation();

        if (null === $locale) {
            return;
        }

        /**
         * @var \Doctrine\Common\Collections\Collection $translations
         */
        $translations = $value->getTranslations();

        /**
         * @var ImageTranslationTrait $trans
         */
        $trans = $translations->get($locale);

        if (null === $trans) {
            $localeDisplayName = Locale::getDisplayLanguage($locale);

            $this->context
                ->buildViolation('image.translation.not_empty')
                ->setParameter('%domain%', $localeDisplayName)
                ->setTranslationDomain('barthy_image_upload')
                ->atPath('translations')
                ->addViolation();
        }
    }
}
