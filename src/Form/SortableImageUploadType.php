<?php

namespace BarthyKoeln\ImageUploadBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class SortableImageUploadType extends ImageUploadType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function getBlockPrefix(): string
    {
        return 'barthy_image_sortable_upload';
    }
}
