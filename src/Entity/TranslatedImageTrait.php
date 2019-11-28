<?php


namespace Barthy\ImageUploadBundle\Entity;

use Barthy\CachedPrezentTranslation\CachedPrezentTranslationTrait;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

trait TranslatedImageTrait
{

    use CachedPrezentTranslationTrait;

    /**
     * @Prezent\Translations(targetEntity="ImageTranslationTrait")
     * @Assert\Valid
     */
    protected $translations;

    public function getTitle(?string $locale = null): ?string
    {
        /**
         * @var ImageTranslationTrait $trans
         */
        $trans = $this->translate($locale);

        return $trans->getTitle();
    }

    public function getAlt(?string $locale = null): ?string
    {
        /**
         * @var ImageTranslationTrait $trans
         */
        $trans = $this->translate($locale);

        return $trans->getAlt();
    }
}
