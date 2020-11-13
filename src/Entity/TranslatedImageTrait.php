<?php

namespace BarthyKoeln\ImageUploadBundle\Entity;

use BarthyKoeln\CachedPrezentTranslation\CachedPrezentTranslationTrait;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

trait TranslatedImageTrait
{
    use CachedPrezentTranslationTrait;

    /**
     * @Assert\Valid
     * @Prezent\Translations(targetEntity="BarthyKoeln\ImageUploadBundle\Entity\ImageTranslationTrait")
     *
     * @var \Doctrine\Common\Collections\Collection
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
