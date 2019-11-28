<?php

namespace Barthy\ImageUploadBundle\Form;


use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Tests\Entity\SpecificImage;
use Barthy\ImageUploadBundle\Tests\Entity\SpecificImageTranslation;
use Barthy\SlugFilenameBundle\DependencyInjection\SlugFilenameSubscriberFactory;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class ImageTypeTest extends KernelTestCase
{

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private static $fileSystem;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager|object
     */
    private $cacheManager;

    /**
     * @var \Doctrine\ORM\EntityManager|object
     */
    private $entityManager;

    /**
     * @var \Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig|object
     */
    private $imageUploadConfig;

    /**
     * @var \Barthy\SlugFilenameBundle\DependencyInjection\SlugFilenameSubscriberFactory|object
     */
    private $slugFactory;

    /**
     * @var object|\Vich\UploaderBundle\Mapping\PropertyMappingFactory
     */
    private $propertyMappingFactory;

    /**
     * @var object|\Symfony\Component\Form\FormFactory
     */
    private $formFactory;


    public static function setUpBeforeClass()
    {
        self::bootKernel();
        self::$fileSystem = self::$container->get('filesystem');

        self::$fileSystem->mkdir(self::$kernel->getProjectDir().'/tests/public/uploads/images', 0774);

        parent::setUpBeforeClass();
    }

    protected function setUp()
    {
        self::bootKernel();

        $this->translator = self::$container->get(TranslatorInterface::class);
        $this->cacheManager = self::$container->get('liip_imagine.cache.manager');
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
        $this->imageUploadConfig = self::$container->get(ImageUploadConfig::class);
        $this->slugFactory = self::$container->get(SlugFilenameSubscriberFactory::class);
        $this->propertyMappingFactory = self::$container->get(PropertyMappingFactory::class);
        $this->formFactory = self::$container->get('form.factory');

        parent::setUp();
    }

    protected function getExtensions()
    {
        $collectionType = new ImageCollectionType($this->translator);

        $imageType = new ImageUploadType(
            $this->cacheManager,
            self::$kernel,
            $this->entityManager,
            $this->imageUploadConfig,
            $this->slugFactory,
            $this->propertyMappingFactory
        );

        $sortableImageType = new SortableImageUploadType(
            $this->cacheManager,
            self::$kernel,
            $this->entityManager,
            $this->imageUploadConfig,
            $this->slugFactory,
            $this->propertyMappingFactory
        );

        return [
            new PreloadedExtension([$imageType, $sortableImageType, $collectionType], []),
        ];
    }

    private static function getValidDataSet()
    {
        $basePath = self::$kernel->getProjectDir().'/tests/Fixtures/Files/';
        $fileName = 'small_image.jpg';

        $copyFileName = "cp-".$fileName;
        $filePath = $basePath.$copyFileName;

        @copy($basePath.$fileName, $filePath);

        $file = new UploadedFile(
            $filePath,
            $copyFileName,
            "image/jpeg",
            null,
            true
        );

        return [
            [
                'translations' => [
                    'de' => [
                        'title' => 'Test',
                        'alt'   => 'Testing',
                    ],
                ],
                'imageFile'    => [
                    'file' => $file,
                ],
                'position'     => 0,
                'x'            => 0,
                'y'            => 0,
                'w'            => 1280,
                'h'            => 720,
            ],
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    private static function getArrayCollection(array &$formData): ArrayCollection
    {
        $object = new ArrayCollection();
        foreach ($formData as $imageData) {
            $image = new SpecificImage();

            foreach ($imageData['translations'] as $locale => $translationData) {
                $translation = new SpecificImageTranslation();
                $translation->setLocale($locale);
                $translation->setTitle($translationData['title']);
                $translation->setAlt($translationData['alt']);

                $image->addTranslation($translation);
            }

            $image->setPosition($imageData['position']);
            $image->setImageFile($imageData['imageFile']['file']);
            $image->setX($imageData['x']);
            $image->setY($imageData['y']);
            $image->setW($imageData['w']);
            $image->setH($imageData['h']);

            $object->add($image);
        }

        return $object;
    }

    private static function sanitizeUpdatedAtDates(ArrayCollection &$object, ArrayCollection &$objectToCompare)
    {
        /**
         * @var SpecificImage $firstImage
         */
        $firstImage = $object->get(0);

        /**
         * @var SpecificImage $firstImage
         */
        $secondImage = $objectToCompare->get(0);

        $date = new DateTime();

        $firstImage->setUpdatedAt($date);
        $secondImage->setUpdatedAt($date);
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::__construct
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::configureOptions
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::buildForm
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::getParent
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::getBlockPrefix
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::preSubmit
     *
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::__construct
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::configureOptions
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::getParent
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::getBlockPrefix
     *
     * @covers \Barthy\ImageUploadBundle\Form\SortableImageUploadType::buildForm
     * @covers \Barthy\ImageUploadBundle\Form\SortableImageUploadType::getBlockPrefix
     * @throws \Exception
     */
    public function testSubmitValidData(): void
    {
        $formData = self::getValidDataSet();

        $objectToCompare = new ArrayCollection();
        $object = self::getArrayCollection($formData);
        $this->submitForm($object, $objectToCompare, $formData);

        self::assertFileExists(self::$kernel->getProjectDir().'/tests/public/uploads/images/test.jpeg');

        /**
         * @var SpecificImage $persistedImage
         */
        $persistedImage = $objectToCompare->get('0');

        $formData[0]['x'] = 100;
        $formData[0]['imageFile'] = null;
        $formData[0]['translations']['de']['title'] = 'Anderer Titel';

        $object->get('0')->setX('100');
        $object->get('0')->setImageFile(null);
        $object->get('0')->setFileName('anderer-titel.jpeg');
        $object->get('0')->setSize($persistedImage->getSize());
        $object->get('0')->setMimeType($persistedImage->getMimeType());
        $object->get('0')->setDimensions($persistedImage->getDimensions());

        $object->get('0')->translate('de')->setTitle('Anderer Titel');

        $this->submitForm($object, $objectToCompare, $formData);

        self::assertFileNotExists(self::$kernel->getProjectDir().'/tests/public/uploads/images/test.jpeg');
        self::assertFileExists(self::$kernel->getProjectDir().'/tests/public/uploads/images/anderer-titel.jpeg');

        $formData[0]['y'] = 100;
        $object->get('0')->setY('100');

        $this->submitForm($object, $objectToCompare, $formData);

        $formData[0]['w'] = 200;
        $object->get('0')->setW('200');

        $this->submitForm($object, $objectToCompare, $formData);

        $formData[0]['h'] = 300;
        $object->get('0')->setH('300');

        $this->submitForm($object, $objectToCompare, $formData);
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::__construct
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::configureOptions
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::buildForm
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::getParent
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::getBlockPrefix
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::preSubmit
     *
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::__construct
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::configureOptions
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::getParent
     * @covers \Barthy\ImageUploadBundle\Form\ImageCollectionType::getBlockPrefix
     *
     * @covers \Barthy\ImageUploadBundle\Form\SortableImageUploadType::buildForm
     * @covers \Barthy\ImageUploadBundle\Form\SortableImageUploadType::getBlockPrefix
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $object
     * @param \Doctrine\Common\Collections\ArrayCollection $objectToCompare
     *
     * @param array                                        $formData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function submitForm(ArrayCollection &$object, ArrayCollection &$objectToCompare, array &$formData): void
    {
        $form = $this->formFactory->create(
            ImageCollectionType::class,
            $objectToCompare,
            [
                'sortable' => true,
            ]
        );

        $form->submit($formData);

        if (false === $form->isValid()) {
            $errors = $form->get('0')->get('imageFile')->getErrors();

            foreach ($errors as $error) {
                echo $error->getOrigin()->getName().': '.$error->getMessage().PHP_EOL;
            }
        }

        self::assertTrue($form->isValid());
        self::assertTrue($form->isSynchronized());

        self::sanitizeUpdatedAtDates($object, $objectToCompare);
        self::assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach ($objectToCompare as $image) {
            if (false === $this->entityManager->contains($image)) {
                $this->entityManager->persist($image);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::configureOptions
     *
     * @expectedException OptionDefinitionException
     */
    public function testAspectHeightResolving()
    {
        self::expectException(OptionDefinitionException::class);
        $objectToCompare = new ArrayCollection();
        $this->formFactory->create(
            ImageCollectionType::class,
            $objectToCompare,
            [
                'sortable'              => true,
                'cropper_aspect_height' => 2,
            ]
        );
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::configureOptions
     *
     * @expectedException OptionDefinitionException
     */
    public function testAspectWidthResolving()
    {
        self::expectException(OptionDefinitionException::class);
        $objectToCompare = new ArrayCollection();
        $this->formFactory->create(
            ImageCollectionType::class,
            $objectToCompare,
            [
                'sortable'             => true,
                'cropper_aspect_width' => 2,
            ]
        );
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Form\ImageUploadType::getBlockPrefix
     */
    public function testBlockPrefix()
    {
        $type = self::$container->get(ImageUploadType::class);
        self::assertEquals('barthy_image_upload', $type->getBlockPrefix());
    }

    public function tearDown()
    {
        $this->translator = null;
        $this->cacheManager = null;
        $this->entityManager = null;
        $this->imageUploadConfig = null;
        $this->slugFactory = null;
        $this->propertyMappingFactory = null;
        $this->formFactory = null;

        self::$fileSystem->remove(self::$kernel->getProjectDir().'/tests/public');

        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        self::$fileSystem = null;
        parent::tearDownAfterClass();
    }

}
