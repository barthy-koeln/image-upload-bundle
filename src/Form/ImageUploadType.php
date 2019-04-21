<?php

namespace Barthy\ImageUploadBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\Image;
use Barthy\SlugFilenameBundle\DependencyInjection\SlugFilenameSubscriberFactory;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Mapping\PropertyMapping;
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
     * @var SlugFilenameSubscriberFactory
     */
    private $slugFilenameSubscriberFactory;

    /**
     * @var \Vich\UploaderBundle\Mapping\PropertyMappingFactory
     */
    private $mappingFactory;

    public function __construct(
        CacheManager $cacheManager,
        KernelInterface $kernel,
        EntityManagerInterface $entityManager,
        ImageUploadConfig $imageUploadConfig,
        SlugFilenameSubscriberFactory $slugFilenameSubscriberFactory,
        PropertyMappingFactory $mappingFactory
    ) {
        $this->cacheManager = $cacheManager;
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
        $this->imageUploadConfig = $imageUploadConfig;
        $this->slugFilenameSubscriberFactory = $slugFilenameSubscriberFactory;
        $this->mappingFactory = $mappingFactory;
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
                                'download_uri' => false,
                                'image_uri' => true,
                                'allow_delete' => false,
                                'error_bubbling' => false,
                                'translation_domain' => 'barthy_admin',
                                'label' => false,
                                'required' => false,
                                'attr' => [
                                    'placeholder' => 'choose_file',
                                    'file_name' => $nullImage === false ? $entity->getFileName() : "",
                                    'crop_data' => $nullImage === false ? $entity->getJSONCropData() : "",
                                    'accept' => $options['accept'],
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
                                'label' => false,
                                'required' => false,
                                'error_bubbling' => false,
                                'fields' => [
                                    'title' => [
                                        'field_type' => TextType::class,
                                        'label' => 'image.title',
                                        'translation_domain' => 'barthy_admin',
                                        'error_bubbling' => true,
                                    ],
                                    'alt' => [
                                        'field_type' => TextType::class,
                                        'label' => 'image.alt',
                                        'translation_domain' => 'barthy_admin',
                                        'error_bubbling' => true,
                                    ],
                                ],
                            ]
                        );
                }
            )
            ->addEventSubscriber(
                $this->slugFilenameSubscriberFactory->create(
                    function ($entity, string $oldName, string $newname, string $uploadPath) {
                        $filePath = $uploadPath.'/'.$oldName;
                        $this->cacheManager->remove($filePath.'/'.$oldName);
                    }
                )
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $this->preSubmit($event);
                }
            );
    }

    public function preSubmit(FormEvent $event)
    {
        /**
         * @var Image $image
         */
        $image = $event->getForm()->getData();

        if ($image !== null) {

            $data = $event->getData();

            $cropDataChanged = $image->getX() !== intval($data['x'])
                || $image->getY() !== intval($data['y'])
                || $image->getW() !== intval($data['w'])
                || $image->getH() !== intval($data['h']);

            $fileChanged = $data['imageFile']['file'] !== null;

            if ($cropDataChanged || $fileChanged) {
                /**
                 * @var PropertyMapping $mapping
                 */
                $mapping = $this->mappingFactory->fromObject($image);
                $mapping = reset($mapping);

                $this->cacheManager->remove($mapping->getUriPrefix().'/'.$image->getFileName());
            }
        }
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Image::class,
                'accept' => 'image/jpeg',
                'cropper_aspect_width' => null,
                'cropper_aspect_height' => null,
                'attr' => function (Options $options) {
                    if (null === $options['cropper_aspect_width'] xor null === $options['cropper_aspect_height']) {
                        throw new OptionDefinitionException(
                            "cropper.js is enabled, but only one aspect ratio option has been defined. Use both the 'cropper_aspect_width' and 'cropper_aspect_height' options."
                        );
                    } else {
                        return [
                            'class' => 'vue-image',
                            'data-aspect-width' => $options['cropper_aspect_width'],
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
