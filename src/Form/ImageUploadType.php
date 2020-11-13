<?php

namespace BarthyKoeln\ImageUploadBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class ImageUploadType extends AbstractType
{
    private KernelInterface $kernel;

    private EntityManagerInterface $entityManager;

    private ImageUploadConfig $imageUploadConfig;

    private PropertyMappingFactory $mappingFactory;

    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $entityManager,
        ImageUploadConfig $imageUploadConfig,
        PropertyMappingFactory $mappingFactory
    ) {
        $this->kernel            = $kernel;
        $this->entityManager     = $entityManager;
        $this->imageUploadConfig = $imageUploadConfig;
        $this->mappingFactory    = $mappingFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) use ($options) {
                    /**
                     * @var \BarthyKoeln\ImageUploadBundle\Entity\ImageInterface $entity
                     */
                    $entity    = $event->getData();
                    $nullImage = null === $entity || null === $entity->getFileName();

                    $event->getForm()
                        ->add(
                            'imageFile',
                            FileType::class,
                            [
                                'error_bubbling'     => false,
                                'translation_domain' => 'barthy_admin',
                                'label'              => false,
                                'required'           => false,
                                'attr'               => [
                                    'placeholder'        => 'choose_file',
                                    'data-file-name'     => false === $nullImage ? $this->getPreviewImagePath(
                                        $entity->getFileName()
                                    ) : '',
                                    'data-crop-data'     => false === $nullImage ? $entity->getJSONCropData() : '',
                                    'accept'             => $options['accept'],
                                    'data-aspect-width'  => $options['cropper_aspect_width'],
                                    'data-aspect-height' => $options['cropper_aspect_height'],
                                ],
                            ]
                        )
                        ->add(
                            'x',
                            HiddenType::class,
                            [
                                'by_reference' => false,
                                'attr'         => [
                                    'class' => 'crop-x',
                                ],
                            ]
                        )
                        ->add(
                            'y',
                            HiddenType::class,
                            [
                                'by_reference' => false,
                                'attr'         => [
                                    'class' => 'crop-y',
                                ],
                            ]
                        )
                        ->add(
                            'w',
                            HiddenType::class,
                            [
                                'by_reference' => false,
                                'attr'         => [
                                    'class' => 'crop-w',
                                ],
                            ]
                        )
                        ->add(
                            'h',
                            HiddenType::class,
                            [
                                'by_reference' => false,
                                'attr'         => [
                                    'class' => 'crop-h',
                                ],
                            ]
                        );

                    if ($options['translations']) {
                        $event->getForm()
                            ->add(
                                'translations',
                                TranslationsType::class,
                                [
                                    'label'           => false,
                                    'required'        => false,
                                    'error_bubbling'  => false,
                                    'attr'            => [
                                        'class' => 'sort-hidden',
                                    ],
                                    'fields'          => [
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
                                    'excluded_fields' => $options['excluded_translation_fields'],
                                ]
                            );
                    }
                }
            );
    }

    private function getPreviewImagePath(string $filename)
    {
        return $this->imageUploadConfig->getImagePathPrefix().DIRECTORY_SEPARATOR.$filename;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $imageClass = $this->imageUploadConfig->getImageClass();
        $metadata   = $this->entityManager->getClassMetadata($imageClass);

        $resolver->setDefaults(
            [
                'data_class'                  => $imageClass,
                'accept'                      => 'image/jpeg',
                'cropper_aspect_width'        => null,
                'cropper_aspect_height'       => null,
                'excluded_translation_fields' => [],
                'translations'                => $metadata->hasAssociation('translations'),
                'crop'                        => function (Options $options) {
                    if (null === $options['cropper_aspect_width'] xor null === $options['cropper_aspect_height']) {
                        throw new OptionDefinitionException("cropper.js is enabled, but only one aspect ratio option has been defined. Use both the 'cropper_aspect_width' and 'cropper_aspect_height' options.");
                    }

                    return null !== $options['cropper_aspect_width'] && null !== $options['cropper_aspect_height'];
                },
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'barthy_image_upload';
    }
}
