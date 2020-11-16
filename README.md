# Image Upload Bundle

[![CircleCI](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/main.svg?style=svg&circle-token=ee369ca101a5ae0c8b1b3d32e1ca7a4e4e8043c6)](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/main)
[![Coverage](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fbadges.barthy.koeln%2Fbadge%2Fimage-upload-bundle%2Fcoverage)](https://circleci.com/gh/barthy-koeln/image-upload-bundle/tree/master)

This bundle provides commonly used functions to upload and handle images in symfony applications.

It is hardwired to certain useful bundles and is meant to integrate their functionality.

## Function Overview

This bundle provides …

- an `Image` entity that has fields for file metadata (mime type, dimensions), sorting, cropping, and translating.
- Uploads using the [vich/uploader-bundle](https://github.com/dustin10/VichUploaderBundle)
- translations of "title" and "alt" tags using the [prezent/doctrine-translatable-bundle](https://github.com/prezent/doctrine-translation-bundle)
- cache handling for the [liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- twig functions to create thumbnails using the [liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- form types for basic image upload, image collections and sortable image collections, with options for integration with cropping and sorting libraries.

## Installation

```shell script
composer require barthy-koeln/image-upload-bundle
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
# config/packages/barthy_koeln_image_upload.yaml

barthy_koeln_image_upload:
  image_class: # [REQUIRED]
  image_path_prefix: # [REQUIRED]
  required_translation: '%locale%'
  max_file_size: 2M
```

## Usage

### Entities

### Forms

Use or extend the form types for single image upload, collection upload or sortable collection upload.

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
                'aspect_width' => 4,
                'aspect_height' => 3,
                'excluded_translation_fields' => ['slug'],
                'accept' => 'image/jpeg'
            ]
        );
    }
    
}
```

- `aspect_width`: `null` (default) or number 
- `apect_height`: `null` (default) or number

If only one is null, an exception is thrown.

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
                'aspect_width' => 16,
                'aspect_height' => 9,
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
