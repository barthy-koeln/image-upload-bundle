<?php

namespace Barthy\ImageUploadBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImageUploadConfig
     */
    private $imageUploadConfig;

    /**
     * @var \Vich\UploaderBundle\Mapping\PropertyMappingFactory
     */
    private $mappingFactory;

    public function __construct(
        CacheManager $cacheManager,
        KernelInterface $kernel,
        EntityManagerInterface $entityManager,
        ImageUploadConfig $imageUploadConfig,
        PropertyMappingFactory $mappingFactory
    ) {
        $this->cacheManager = $cacheManager;
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
        $this->imageUploadConfig = $imageUploadConfig;
        $this->mappingFactory = $mappingFactory;
    }

    private function getPreviewImagePath(string $filename)
    {
        return $this->imageUploadConfig->getImagePathPrefix().DIRECTORY_SEPARATOR.$filename;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) use ($options) {

                    /**
                     * @var \Barthy\ImageUploadBundle\Entity\ImageInterface $entity
                     */
                    $entity = $event->getData();
                    $nullImage = $entity === null || $entity->getFileName() === null;

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
                                    'placeholder'    => 'choose_file',
                                    'data-file-name' => $nullImage === false ? $this->getPreviewImagePath(
                                        $entity->getFileName()
                                    ) : "",
                                    'data-crop-data' => $nullImage === false ? $entity->getJSONCropData() : "",
                                    'accept'         => $options['accept'],
                                    'data-aspect-width'  => $options['cropper_aspect_width'],
                                    'data-aspect-height' => $options['cropper_aspect_height'],
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
                        );

                    if ($options['translations']) {
                        $event->getForm()
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
                }
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'preSubmit']
            );
    }

    public function preSubmit(FormEvent $event)
    {

        /**
         * @var \Barthy\ImageUploadBundle\Entity\ImageInterface $image
         */
        $image = $event->getForm()->getData();

        $unitOfWork = $this->entityManager->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($image);

        if (!empty($changeSet)) {
            $this->cacheManager->remove($this->getPreviewImagePath($image->getFileName()));
        }
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $imageClass = $this->imageUploadConfig->getImageClass();
        $metadata = $this->entityManager->getClassMetadata($imageClass);

        $resolver->setDefaults(
            [
                'data_class'            => $imageClass,
                'accept'                => 'image/jpeg',
                'cropper_aspect_width'  => null,
                'cropper_aspect_height' => null,
                'translations'          => $metadata->hasAssociation('translations'),
                'crop'                  => function (Options $options) {
                    if (null === $options['cropper_aspect_width'] xor null === $options['cropper_aspect_height']) {
                        throw new OptionDefinitionException(
                            "cropper.js is enabled, but only one aspect ratio option has been defined. Use both the 'cropper_aspect_width' and 'cropper_aspect_height' options."
                        );
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
