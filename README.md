# Image Upload Bundle

[![CircleCI](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/master.svg?style=svg&circle-token=ee369ca101a5ae0c8b1b3d32e1ca7a4e4e8043c6)](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/master)
[![Coverage](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fbadges.barthy.koeln%2Fbadge%2Fimage-upload-bundle%2Fcoverage)](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/master)

This bundle provides commonly used functions to upload and handle images in symfony applications.
It primarily is meant to work with [barthy/admin-bundle](httsp://github.com/BarthyB/admin-bundle).

It it hardwired to certain useful bundles and is meant to integrate their functionality.

## Function Overview

This bundle provides …

- an `Image` entity that has fields for file metadata (mime type, dimensions), sorting, cropping, and translating. 
- translations of "title" and "alt" tags using the [prezent/doctrine-translatable-bundle](https://github.com/prezent/doctrine-translation-bundle)  
- file name handling using the [barthy/slug-filename-bundle](https://github.com/BarthyB/slug-filename-bundle)
- cache handling for the [liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- twig functions to create thumbnails using the [liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- error and assertion handling
- configurable maximum file size and file name language
- form types for basic image upload, image collections and sortable image collections
- form type options to integrate with [cropper.js](https://github.com/fengyuanchen/cropperjs) through the [barthy/admin-bundle](httsp://github.com/BarthyB/admin-bundle)
- form type options to integrate with [sortable.js](https://github.com/SortableJS/Sortable) through the [barthy/admin-bundle](httsp://github.com/BarthyB/admin-bundle)

## Installation

```json
{
    "require": {
        "barthy/image-upload-bundle": "dev-master"
    },
    "repositories": [
        {
          "type": "path",
          "url": "../barthy/image-upload-bundle/",
          "options": {
            "symlink": true
          }
        },
        {
          "type": "vcs",
          "url": "https://github.com/BarthyB/image-upload-bundle.git"
        }
    ]
}
```

```php
<?php

/** config/bundles.php */

return [
    // […]
    BarthyKoeln\ImageUploadBundle\BarthyKoelnImageUploadBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/barthy_image_upload.yaml

barthy_image_upload:
  max_file_size: 2M
  file_name_language: '%locale%'
```

## Usage

### Entities

#### OneToOne Image Relation

```php
<?php

use BarthyKoeln\ImageUploadBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Project 
{
    
    /**
     * @var Image
     *
     * @Assert\Valid
     *
     * @ORM\OneToOne(targetEntity="BarthyKoeln\ImageUploadBundle\FileSizeEntity\Image", cascade={"all"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $image;
    
    /**
     * @return Image
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @param Image|null $image
     */
    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }    
    
}
```

#### OneToMany Image Relation

```php
<?php

use BarthyKoeln\ImageUploadBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Project 
{

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\ManyToMany(targetEntity="BarthyKoeln\ImageUploadBundle\FileSizeEntity\Image", cascade={"all"})
     * @ORM\JoinTable(name="slider_modules_images",
     *     joinColumns={@ORM\JoinColumn(name="slider_module_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *     )
     */
    private $images;
    
     public function __construct()
     {
         $this->images = new ArrayCollection();
     }
     
     /**
      * @Assert\Callback
      * @param ExecutionContextInterface $context
      */
     public function validate(ExecutionContextInterface $context)
     {
         if (null === $this->getImages() || $this->getImages()->count() < 1) {
             $context
                 ->buildViolation('slider.images.not_empty')
                 ->setTranslationDomain('validation')
                 ->atPath('images')
                 ->addViolation();
         }
     }

    /**
     * @return Collection|null
     */
    public function getImages(): ?Collection
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection|null $images
     */
    public function setImages(?ArrayCollection $images): void
    {
        $this->images = $images;
    }

    /**
     * @param Image $image
     */
    public function addImage(Image $image): void
    {
        $this->images->add($image);
    }

    /**
     * @param Image $image
     */
    public function removeImage(Image $image): void
    {
        $this->images->remove($image);
    }
    
}
```

### Forms

#### Single Image Upload

```php
<?php

use BarthyKoeln\ImageUploadBundle\Form\ImageUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectAdminType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'image',
            ImageUploadType::class,
            [
                'cropper_aspect_width' => 1.19,
                'cropper_aspect_height' => 1,
            ]
        );
    }
    
}
```


- `cropper_aspect_width`: `null` or number 
- `cropper_aspect_height`: `null` or number
- if both are null, cropper.js will be disabled. If only one is null, an exception is thrown.

#### Multiple Image Upload

```php
<?php

use BarthyKoeln\ImageUploadBundle\Form\ImageCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectAdminType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'images',
            ImageCollectionType::class,
            [
                'sortable' => true,
                'cropper_aspect_width' => 1.19,
                'cropper_aspect_height' => 1,
            ]
        );
    }
    
}
```

- `cropper_aspect_width`, `cropper_aspect_height`: as mentioned above
- `sortable`: `true` or `false`, enables a hidden field `position` with the class `sortable-field`

### Twig

The `thumbnailParams` function creates an array that can be used with the [liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle).

```twig
{% set runtimeConfig = image|thumbnailParams(500, 400) %}
<img class="img-fluid"
     data-index="{{ loop.index0 }}"
     data-full-src="{{ asset('/uploads/images/' ~ image)|imagine_filter('compression') }}"
     data-full-dimensions="{{ image.dimensions|json_encode|e }}"
     src="{{ asset('/uploads/images/' ~ image)|imagine_filter('compression', runtimeConfig) }}"
     title="{{ image.title }}"
     alt="{{ image.alt }}"
>
```

- `width`, `height`: the final thumbnail size
- `mode`: either "inset" or "outbound" ([see the symfony docs for more detail](https://symfony.com/doc/master/bundles/LiipImagineBundle/filters/sizing.html#thumbnail-options))
