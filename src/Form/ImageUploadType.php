<?php

namespace BarthyKoeln\ImageUploadBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Entity\ImageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['crop']) {
            $this->addCropFields($builder);
        }

        if ($options['translations']) {
            $this->addTranslationFields($builder, $options);
        }

        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                fn (FormEvent $event) => $this->addImageField($event->getData(), $event->getForm(), $options)
            );
    }

    private function addCropFields(FormBuilderInterface $form): void
    {
        $form
            ->add(
                'x',
                HiddenType::class,
                [
                    'attr'         => [
                        'class' => 'crop-x',
                    ],
                ]
            )
            ->add(
                'y',
                HiddenType::class,
                [
                    'attr'         => [
                        'class' => 'crop-y',
                    ],
                ]
            )
            ->add(
                'w',
                HiddenType::class,
                [
                    'attr'         => [
                        'class' => 'crop-w',
                    ],
                ]
            )
            ->add(
                'h',
                HiddenType::class,
                [
                    'attr'         => [
                        'class' => 'crop-h',
                    ],
                ]
            );
    }

    private function addTranslationFields(FormBuilderInterface $form, array $options): void
    {
        $form
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

    private function addImageField(?ImageInterface $image, FormInterface $form, array $options): void
    {
        $nullImage = null === $image || null === $image->getFileName();

        $form
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
                            $image->getFileName()
                        ) : '',
                        'data-crop-data'     => false === $nullImage ? $image->getJSONCropData() : '',
                        'accept'             => $options['accept'],
                        'data-aspect-width'  => $options['aspect_width'],
                        'data-aspect-height' => $options['aspect_height'],
                    ],
                ]
            );
    }

    private function getPreviewImagePath(string $filename): string
    {
        return $this->imageUploadConfig->getImagePathPrefix().DIRECTORY_SEPARATOR.$filename;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $imageClass = $this->imageUploadConfig->getImageClass();
        $metadata   = $this->entityManager->getClassMetadata($imageClass);

        $resolver->setDefaults(
            [
                'data_class'                  => $imageClass,
                'accept'                      => 'image/jpeg',
                'aspect_width'                => null,
                'aspect_height'               => null,
                'excluded_translation_fields' => [],
                'translations'                => $metadata->hasAssociation('translations'),
                'crop'                        => function (Options $options) {
                    if (null === $options['aspect_width'] xor null === $options['aspect_height']) {
                        throw new OptionDefinitionException(
                            'Only one aspect ratio option has been defined. Use both the "aspect_width" and "aspect_height" options.'
                        );
                    }

                    return null !== $options['aspect_width'] && null !== $options['aspect_height'];
                },
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'barthy_image_upload';
    }
}
