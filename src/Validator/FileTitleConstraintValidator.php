<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 30.01.19
 * Time: 17:47
 */

namespace Barthy\ImageUploadBundle\Validator;


use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\Image;
use Barthy\ImageUploadBundle\Entity\ImageTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileTitleConstraintValidator extends ConstraintValidator
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

        $locale = $this->config->getFileNameLanguage();

        /**
         * @var ImageTranslation $trans
         */
        $trans = $value->getTranslations()->get($locale);

        if (null === $trans) {
            $localeDisplayName = \Locale::getDisplayLanguage($locale);

            $this->context
                ->buildViolation('image.translation.not_empty')
                ->setParameter('%domain%', $localeDisplayName)
                ->setTranslationDomain('barthy_image_upload')
                ->atPath('translations')
                ->addViolation();
        }
    }
}
