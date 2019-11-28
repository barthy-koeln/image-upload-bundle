<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 18.06.18
 * Time: 20:46
 */

namespace Barthy\ImageUploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Locale;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ImageTranslationTrait
 * @package Barthy\ImageUploadBundle\Entity
 */
trait ImageTranslationTrait
{

    abstract function getLocale();

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
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

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt(?string $alt): void
    {
        $this->alt = $alt;
    }
}
