<?php

namespace Tests\Form;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Form\ImageCollectionType;
use BarthyKoeln\ImageUploadBundle\Form\ImageUploadType;
use BarthyKoeln\ImageUploadBundle\Form\SortableImageUploadType;
use Codeception\AssertThrows;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Entity\SpecificImage;
use Tests\Entity\SpecificImageTranslation;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class ImageTypeTest extends KernelTestCase
{
    use AssertThrows;

    private static ?Filesystem $fileSystem;

    private ?TranslatorInterface $translator;

    private ?EntityManagerInterface $entityManager;

    private ?ImageUploadConfig $imageUploadConfig;

    private ?PropertyMappingFactory $propertyMappingFactory;

    private ?FormFactoryInterface $formFactory;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        self::$fileSystem = self::$container->get('filesystem');

        self::$fileSystem->mkdir(self::$kernel->getProjectDir().'/tests/public/uploads/images', 0774);

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        self::$fileSystem = null;
        parent::tearDownAfterClass();
    }

    /**
     * @throws \Exception
     */
    public function testSubmitValidData(): void
    {
        $formData = self::getValidDataSet();

        $objectToCompare = new ArrayCollection();
        $object          = self::getArrayCollection($formData);
        $this->submitForm($object, $objectToCompare, $formData);
        self::assertFileExists(
            self::$kernel->getProjectDir().'/tests/public/uploads/images/'.$object->get(0)->getFileName()
        );

        $object->get(0)->setImageFile(null);
        $objectToCompare->get(0)->setImageFile(null);
        $formData[0]['imageFile'] = null;
        $this->submitForm($object, $objectToCompare, $formData);
        self::assertFileExists(
            self::$kernel->getProjectDir().'/tests/public/uploads/images/'.$object->get(0)->getFileName()
        );
    }

    private static function getValidDataSet(bool $crop = false): array
    {
        $basePath = self::$kernel->getProjectDir().'/tests/Fixtures/Files/';
        $fileName = 'small_image.jpg';

        $copyFileName = 'cp-'.$fileName;
        $filePath     = $basePath.$copyFileName;

        @copy($basePath.$fileName, $filePath);

        $file = new UploadedFile(
            $filePath,
            $copyFileName,
            'image/jpeg',
            null,
            true
        );

        $data = [
            'translations' => [
                'de' => [
                    'title' => 'Test',
                    'alt'   => 'Testing',
                ],
            ],
            'imageFile'    => $file,
            'position'     => 0,
        ];

        if ($crop) {
            $data = array_merge(
                $data,
                [
                    'x' => 0,
                    'y' => 0,
                    'w' => 1280,
                    'h' => 720,
                ]
            );
        }

        return [$data];
    }

    /**
     * @throws \Exception
     */
    private static function getArrayCollection(array $formData): ArrayCollection
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
            $image->setImageFile($imageData['imageFile']);

            $image->setX($imageData['x'] ?? null);
            $image->setY($imageData['y'] ?? null);
            $image->setW($imageData['w'] ?? null);
            $image->setH($imageData['h'] ?? null);

            $object->add($image);
        }

        return $object;
    }

    private function submitForm(ArrayCollection $object, ArrayCollection $objectToCompare, array $formData): void
    {
        $form = $this->formFactory->create(
            ImageCollectionType::class,
            $objectToCompare,
            [
                'sortable' => true,
            ]
        );

        $form->submit($formData);

        self::assertTrue($form->isValid());
        self::assertTrue($form->isSynchronized());

        self::sanitizeUpdatedAtDates($object, $objectToCompare);

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach ($objectToCompare as $image) {
            if (false === $this->entityManager->contains($image)) {
                $image->setCreatedAt(new DateTime());
                $this->entityManager->persist($image);
            }
        }

        $this->entityManager->flush();
    }

    private static function sanitizeUpdatedAtDates(ArrayCollection $object, ArrayCollection $objectToCompare)
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

    public function testAspectHeightResolving()
    {
        $this->assertThrows(
            OptionDefinitionException::class,
            function () {
                $objectToCompare = new ArrayCollection();
                $this->formFactory->create(
                    ImageCollectionType::class,
                    $objectToCompare,
                    [
                        'sortable'      => true,
                        'aspect_height' => 2,
                    ]
                );
            }
        );
    }

    public function testAspectWidthResolving()
    {
        $this->assertThrows(
            OptionDefinitionException::class,
            function () {
                $objectToCompare = new ArrayCollection();
                $this->formFactory->create(
                    ImageCollectionType::class,
                    $objectToCompare,
                    [
                        'sortable'     => true,
                        'aspect_width' => 2,
                    ]
                );
            }
        );
    }

    public function tearDown(): void
    {
        $this->translator             = null;
        $this->entityManager          = null;
        $this->imageUploadConfig      = null;
        $this->propertyMappingFactory = null;
        $this->formFactory            = null;

        self::$fileSystem->remove(self::$kernel->getProjectDir().'/tests/public');

        parent::tearDown();
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->translator             = self::$container->get(TranslatorInterface::class);
        $this->entityManager          = self::$container->get(EntityManagerInterface::class);
        $this->imageUploadConfig      = self::$container->get(ImageUploadConfig::class);
        $this->propertyMappingFactory = self::$container->get(PropertyMappingFactory::class);
        $this->formFactory            = self::$container->get(FormFactoryInterface::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $collectionType = new ImageCollectionType($this->translator);

        $imageType = new ImageUploadType(
            self::$kernel,
            $this->entityManager,
            $this->imageUploadConfig,
            $this->propertyMappingFactory
        );

        $sortableImageType = new SortableImageUploadType(
            self::$kernel,
            $this->entityManager,
            $this->imageUploadConfig,
            $this->propertyMappingFactory
        );

        return [
            new PreloadedExtension([$imageType, $sortableImageType, $collectionType], []),
        ];
    }
}
