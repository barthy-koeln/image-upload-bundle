<?php


namespace Barthy\ImageUploadBundle\Entity;


use Cocur\Slugify\Slugify;

trait TitleFileNameTrait
{

    abstract function getTitle(?string $locale = null): ?string;

    public function getSlugFieldValue(string $locale): ?string
    {
        return $this->getTitle($locale);
    }

    public function getSlugFieldName(): string
    {
        return 'title';
    }

    public function getSlug(string $locale): ?string
    {
        $slug = mb_strtolower($this->getTitle($locale));
        $slugify = new Slugify();
        $slug = $slugify->slugify($slug);

        return $slug;
    }
}
