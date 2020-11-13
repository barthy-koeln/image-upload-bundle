<?php

namespace Tests\Entity;

use BarthyKoeln\ImageUploadBundle\Entity\ImageTranslationTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class ImageTest extends KernelTestCase
{
    protected SpecificImage $image;

    public function setUp(): void
    {
        self::bootKernel();

        $this->image = new SpecificImage();

        parent::setUp();
    }

    public function testConstruct()
    {
        self::assertInstanceOf(ArrayCollection::class, $this->image->getTranslations());
    }

    public function testGetJSONCropData()
    {
        self::assertEmpty($this->image->getJSONCropData());

        $this->image->setX(5);
        $this->image->setY(10);
        $this->image->setW(500);
        $this->image->setH(1000);
        $this->image->setDimensions([1920, 1080]);

        self::assertEquals(
            json_encode(
                [
                    'x'        => 5,
                    'y'        => 10,
                    'width'    => 500,
                    'height'   => 1000,
                    'original' => [1920, 1080],
                ]
            ),
            $this->image->getJSONCropData()
        );
    }

    public function testToString()
    {
        $this->image->setFileName(null);
        self::assertEmpty($this->image->__toString());

        $this->image->setFileName('test.jpg');
        self::assertEquals('test.jpg', $this->image->__toString());
    }

    public function testPositionFunctions()
    {
        $this->image->setPosition(0);
        self::assertEquals(0, $this->image->getPosition());
    }

    public function testCropAndDimensionFunctions()
    {
        $this->image->setX(100);
        self::assertEquals(100, $this->image->getX());
        $this->image->setY(200);
        self::assertEquals(200, $this->image->getY());
        $this->image->setW(300);
        self::assertEquals(300, $this->image->getW());
        $this->image->setH(400);
        self::assertEquals(400, $this->image->getH());

        $this->image->setDimensions([1920, 1080]);
        self::assertEquals([1920, 1080], $this->image->getDimensions());
    }

    public function testFilenameFunctions()
    {
        $this->image->setFileName('file.jpg');
        self::assertEquals('file.jpg', $this->image->getFileName());
    }

    public function testMimeTypeFunctions()
    {
        $this->image->setMimeType('image/jpeg');
        self::assertEquals('image/jpeg', $this->image->getMimeType());
    }

    public function testSizeFunctions()
    {
        $this->image->setSize(200);
        self::assertEquals(200, $this->image->getSize());
    }

    public function testIdFunctions()
    {
        self::assertNull($this->image->getId());
    }

    public function testUpdatedAtFunctions()
    {
        $dateTime = new DateTime();
        $this->image->setUpdatedAt($dateTime);
        self::assertEquals($dateTime, $this->image->getUpdatedAt());
    }

    /**
     * @throws \Exception
     */
    public function testImageFileFunctions()
    {
        $date = new DateTime('last year');
        $this->image->setUpdatedAt($date);

        $file = new File('test.jpg', false);
        $this->image->setImageFile($file);

        self::assertEquals($file, $this->image->getImageFile());
        self::assertNotEquals($date, $this->image->getUpdatedAt());

        $date = $this->image->getUpdatedAt();
        $this->image->setImageFile(null);
        self::assertEquals($date, $this->image->getUpdatedAt());
    }

    public function testTranslations()
    {
        $translation = new SpecificImageTranslation();
        $translation->setLocale('de');
        $translation->setTitle('äöüßéèê');
        $translation->setAlt('test');

        $this->image->addTranslation($translation);

        /**
         * @var ImageTranslationTrait $currentTranslation
         */
        $currentTranslation = $this->image->translate('de');

        self::assertEquals($currentTranslation, $translation);

        self::assertEquals('test', $currentTranslation->getAlt());
        self::assertEquals('äöüßéèê', $currentTranslation->getTitle());

        self::assertEquals('test', $this->image->getAlt('de'));
        self::assertEquals('äöüßéèê', $this->image->getTitle('de'));
    }

    /**
     * @throws \Exception
     */
    public function testValidation()
    {
        $validator = self::$container->get('validator');

        $this->image->setFileName(null);
        $this->image->setImageFile(null);

        $violations = $validator->validate($this->image);
        $this->assertEquals(2, $violations->count());

        $translation = new SpecificImageTranslation();
        $translation->setLocale('de');
        $translation->setTitle('äöüßéèê');
        $translation->setAlt('test');
        $this->image->addTranslation($translation);

        $this->image->setImageFile(new File(__DIR__.'/../Fixtures/Files/big_image.jpg', true));

        $violations = $validator->validate($this->image);
        $this->assertEquals(1, $violations->count());

        $this->image->setImageFile(new File(__DIR__.'/../Fixtures/Files/small_image.jpg', true));
        $violations = $validator->validate($this->image);
        $this->assertEquals(0, $violations->count());

        $translation = new SpecificImageTranslation();
        $translation->setLocale('en');
        $this->image->addTranslation($translation);

        $violations = $validator->validate($this->image);
        $this->assertEquals(2, $violations->count());
    }
}
