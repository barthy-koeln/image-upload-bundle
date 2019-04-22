<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 15.10.18
 * Time: 22:26
 */

namespace Barthy\ImageUploadBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageCollectionType extends AbstractType
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $prepend = $this->translator->trans('image.collection.prepend', [], 'barthy_admin');
        $append = $this->translator->trans('image.collection.append', [], 'barthy_admin');
        $add = $this->translator->trans('image.collection.add', [], 'barthy_admin');

        $resolver->setDefaults(
            [
                'accept' => 'image/jpeg',
                'cropper_aspect_width' => null,
                'cropper_aspect_height' => null,
                'sortable' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'translation_domain' => 'admin',
                'label' => 'article.images.label',
                'prototype_placeholder' => '_images_',
                'entry_type' => function (Options $options) {
                    return $options['sortable'] ? SortableImageUploadType::class : ImageUploadType::class;
                },
                'prototype_name' => function (Options $options) {
                    return $options['prototype_placeholder'];
                },
                'attr' => function (Options $options) use ($prepend, $append, $add) {
                    return [
                        'class' => 'vue-collection',
                        'data-prepend-title' => $prepend,
                        'data-append-title' => $append,
                        'data-add-title' => $add,
                        'data-sortable' => 'true',
                        'data-prototype-placeholder' => $options['prototype_placeholder'],
                    ];
                },
                'entry_options' => function (Options $options) {
                    return [
                        'label' => false,
                        'accept' => $options['accept'],
                        'cropper_aspect_width' => $options['cropper_aspect_width'],
                        'cropper_aspect_height' => $options['cropper_aspect_height'],
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