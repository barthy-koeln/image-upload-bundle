<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 15.10.18
 * Time: 22:26.
 */

namespace BarthyKoeln\ImageUploadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageCollectionType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $prepend = $this->translator->trans('image.collection.prepend', [], 'barthy_image_upload');
        $append  = $this->translator->trans('image.collection.append', [], 'barthy_image_upload');
        $add     = $this->translator->trans('image.collection.add', [], 'barthy_image_upload');
        $sort    = $this->translator->trans('image.collection.sort', [], 'barthy_image_upload');

        $resolver->setDefaults(
            [
                'accept'                      => 'image/jpeg',
                'aspect_width'                => null,
                'aspect_height'               => null,
                'sortable'                    => false,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'translation_domain'          => 'admin',
                'label'                       => 'article.images.label',
                'excluded_translation_fields' => [],
                'entry_classes'               => '',
                'collection_classes'          => '',
                'prototype_placeholder'       => '_images_',
                'entry_type'                  => function (Options $options) {
                    return $options['sortable'] ? SortableImageUploadType::class : ImageUploadType::class;
                },
                'prototype_name'              => function (Options $options) {
                    return $options['prototype_placeholder'];
                },
                'attr'                        => function (Options $options) use ($prepend, $append, $add, $sort) {
                    return [
                        'data-prototype-placeholder' => $options['prototype_placeholder'],
                        'data-allow-add'             => var_export($options['allow_add'], true),
                        'data-sortable'              => var_export($options['sortable'], true),
                        'data-prepend-title'         => $prepend,
                        'data-append-title'          => $append,
                        'data-add-title'             => $add,
                        'data-sort-title'            => $sort,
                        'class'                      => $options['collection_classes'],
                    ];
                },
                'entry_options'               => function (Options $options) {
                    return [
                        'label'                       => false,
                        'accept'                      => $options['accept'],
                        'aspect_width'                => $options['aspect_width'],
                        'aspect_height'               => $options['aspect_height'],
                        'excluded_translation_fields' => $options['excluded_translation_fields'],
                        'attr'                        => [
                            'class' => $options['entry_classes'],
                        ],
                    ];
                },
            ]
        );
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    public function getBlockPrefix()
    {
        return 'barthy_image_collection';
    }
}
