<?php

namespace Barthy\ImageUploadBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Barthy\ImageUploadBundle\Entity\Image;
use Barthy\SlugFilenameBundle\DependencyInjection\SlugFilenameSubscriberFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageUploadType extends AbstractType
{

    /**
     * @var SlugFilenameSubscriberFactory
     */
    private $slugFilenameSubscriberFactory;

    public function __construct(SlugFilenameSubscriberFactory $slugFilenameSubscriberFactory) {
        $this->slugFilenameSubscriberFactory = $slugFilenameSubscriberFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) use ($options) {

                    /**
                     * @var Image $entity
                     */
                    $entity = $event->getData();
                    $nullImage = $entity === null || $entity->getFileName() === null;

                    $event->getForm()
                        ->add(
                            'imageFile',
                            VichImageType::class,
                            [
                                'download_uri'       => false,
                                'image_uri'          => true,
                                'allow_delete'       => false,
                                'error_bubbling'     => false,
                                'translation_domain' => 'barthy_admin',
                                'label'              => false,
                                'required'           => false,
                                'attr'               => [
                                    'placeholder' => 'choose_file',
                                    'file_name'   => $nullImage === false ? $entity->getFileName() : "",
                                    'crop_data'   => $nullImage === false ? $entity->getJSONCropData() : "",
                                    'accept'      => $options['accept'],
                                ],
                            ]
                        )
                        ->add(
                            'x',
                            HiddenType::class,
                            [
                                'attr' => [
                                    'class' => 'crop-x',
                                ],
                            ]
                        )
                        ->add(
                            'y',
                            HiddenType::class,
                            [
                                'attr' => [
                                    'class' => 'crop-y',
                                ],
                            ]
                        )
                        ->add(
                            'w',
                            HiddenType::class,
                            [
                                'attr' => [
                                    'class' => 'crop-w',
                                ],
                            ]
                        )
                        ->add(
                            'h',
                            HiddenType::class,
                            [
                                'attr' => [
                                    'class' => 'crop-h',
                                ],
                            ]
                        )
                        ->add(
                            'translations',
                            TranslationsType::class,
                            [
                                'label'          => false,
                                'required'       => false,
                                'error_bubbling' => false,
                                'attr'           => [
                                    'class' => 'sort-hidden',
                                ],
                                'fields'         => [
                                    'title' => [
                                        'field_type'         => TextType::class,
                                        'label'              => 'image.title',
                                        'translation_domain' => 'barthy_admin',
                                        'error_bubbling'     => true,
                                    ],
                                    'alt'   => [
                                        'field_type'         => TextType::class,
                                        'label'              => 'image.alt',
                                        'translation_domain' => 'barthy_admin',
                                        'error_bubbling'     => true,
                                    ],
                                ],
                            ]
                        );
                }
            )
            ->addEventSubscriber($this->slugFilenameSubscriberFactory->create());
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'            => Image::class,
                'accept'                => 'image/jpeg',
                'cropper_aspect_width'  => null,
                'cropper_aspect_height' => null,
                'attr'                  => function (Options $options) {
                    if (null === $options['cropper_aspect_width'] xor null === $options['cropper_aspect_height']) {
                        throw new OptionDefinitionException(
                            "cropper.js is enabled, but only one aspect ratio option has been defined. Use both the 'cropper_aspect_width' and 'cropper_aspect_height' options."
                        );
                    } else {
                        return [
                            'class'              => 'vue-image',
                            'data-aspect-width'  => $options['cropper_aspect_width'],
                            'data-aspect-height' => $options['cropper_aspect_height'],
                        ];
                    }
                },
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'barthy_image_upload';
    }

}
