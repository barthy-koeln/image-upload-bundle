<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 31.01.19
 * Time: 22:40
 */

namespace Barthy\ImageUploadBundle\Test\Entity;

use Barthy\ImageUploadBundle\Entity\Image;
use Barthy\ImageUploadBundle\Entity\ImageTranslation;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class ImageTest extends KernelTestCase
{

    /**
     * @var Image
     */
    protected $image;

    public function setUp()
    {
        self::bootKernel();

        $this->image = new Image();

        parent::setUp();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::__construct
     */
    public function test__construct()
    {
        self::assertInstanceOf(ArrayCollection::class, $this->image->getTranslations());
    }


    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getSlug
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getSlugFieldName
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getSlugFieldValue
     */
    public function testGetSlug()
    {
        $translation = new ImageTranslation();

        $translation->setLocale('de');
        $translation->setTitle('äöüßéèê');
        $translation->setAlt('test');
        $this->image->addTranslation($translation);

        $slug = $this->image->getSlug('de');

        self::assertEquals('title', $this->image->getSlugFieldName());
        self::assertEquals('äöüßéèê', $this->image->getSlugFieldValue('de'));
        self::assertEquals('aeoeuesseee', $slug);
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getJSONCropData
     */
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
                    "x" => 5,
                    "y" => 10,
                    "width" => 500,
                    "height" => 1000,
                    "original" => [1920, 1080],
                ]
            ),
            $this->image->getJSONCropData()
        );
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::__toString
     */
    public function test__toString()
    {
        $this->image->setFileName(null);
        self::assertEmpty($this->image->__toString());

        $this->image->setFileName('test.jpg');
        self::assertEquals('test.jpg', $this->image->__toString());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setPosition
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getPosition
     */
    public function testPositionFunctions()
    {
        $this->image->setPosition(0);
        self::assertEquals(0, $this->image->getPosition());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setX
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getX
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getY
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setY
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getW
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setW
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getH
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setH
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getDimensions
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setDimensions
     */
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

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setFileName
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getFileName
     */
    public function testFilenameFunctions()
    {
        $this->image->setFileName('file.jpg');
        self::assertEquals('file.jpg', $this->image->getFileName());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setMimeType
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getMimeType
     */
    public function testMimeTypeFunctions()
    {
        $this->image->setMimeType('image/jpeg');
        self::assertEquals('image/jpeg', $this->image->getMimeType());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setSize
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getSize
     */
    public function testSizeFunctions()
    {
        $this->image->setSize(200);
        self::assertEquals(200, $this->image->getSize());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getId
     */
    public function testIdFunctions()
    {
        self::assertNull($this->image->getId());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setUpdatedAt
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getUpdatedAt
     * @throws \Exception
     */
    public function testUpdatedAtFunctions()
    {
        $dateTime = new DateTime();
        $this->image->setUpdatedAt($dateTime);
        self::assertEquals($dateTime, $this->image->getUpdatedAt());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::setImageFile
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getImageFile
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

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::addTranslation
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getAlt
     * @covers \Barthy\ImageUploadBundle\Entity\Image::getTitle
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::setLocale
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::setTitle
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::getTitle
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::setAlt
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::getAlt
     */
    public function testTranslations()
    {
        $translation = new ImageTranslation();
        $translation->setLocale('de');
        $translation->setTitle('äöüßéèê');
        $translation->setAlt('test');

        $this->image->addTranslation($translation);

        /**
         * @var ImageTranslation $currentTranslation
         */
        $currentTranslation = $this->image->translate('de');

        self::assertEquals($currentTranslation, $translation);

        self::assertEquals('test', $currentTranslation->getAlt());
        self::assertEquals('äöüßéèê', $currentTranslation->getTitle());

        self::assertEquals('test', $this->image->getAlt('de'));
        self::assertEquals('äöüßéèê', $this->image->getTitle('de'));
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Entity\Image::validate
     * @covers \Barthy\ImageUploadBundle\Entity\ImageTranslation::validate
     * @throws \Exception
     */
    public function testValidation()
    {
        $validator = self::$container->get('validator');

        $this->image->setFileName(null);
        $this->image->setImageFile(null);

        $violations = $validator->validate($this->image);
        $this->assertEquals(2, $violations->count());

        $translation = new ImageTranslation();
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

        $translation = new ImageTranslation();
        $translation->setLocale('en');
        $this->image->addTranslation($translation);

        $violations = $validator->validate($this->image);
        $this->assertEquals(2, $violations->count());
    }
}
