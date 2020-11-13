<?php

namespace BarthyKoeln\ImageUploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Locale;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ImageTranslationTrait.
 */
trait ImageTranslationTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title = null;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $alt = null;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $localeDisplayName = Locale::getDisplayLanguage($this->getLocale());

        if (empty($this->getTitle())) {
            $context
                ->buildViolation('image.title.not_empty')
                ->setParameter('%domain%', $localeDisplayName)
                ->setTranslationDomain('barthy_image_upload')
                ->atPath('title')
                ->addViolation();
        }

        if (empty($this->getAlt())) {
            $context
                ->buildViolation('image.alt.not_empty')
                ->setParameter('%domain%', $localeDisplayName)
                ->setTranslationDomain('barthy_image_upload')
                ->atPath('alt')
                ->addViolation();
        }
    }

    abstract public function getLocale();

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): void
    {
        $this->alt = $alt;
    }
}
