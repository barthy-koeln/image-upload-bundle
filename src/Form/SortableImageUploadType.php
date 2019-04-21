<?php

namespace Barthy\ImageUploadBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class SortableImageUploadType extends ImageUploadType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'position',
                HiddenType::class,
                [
                    'attr' => [
                        'class' => 'sortable-field',
                    ],
                ]
            );

        parent::buildForm($builder, $options);
    }

    public function getBlockPrefix()
    {
        return 'barthy_image_sortable_upload';
    }
}
