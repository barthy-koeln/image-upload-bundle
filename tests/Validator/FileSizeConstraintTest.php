<?php

namespace Barthy\ImageUploadBundle\Test\Validator;

use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Validator\FileSizeConstraint;
use Barthy\ImageUploadBundle\Validator\FileSizeConstraintValidator;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class FileSizeEntity
{

    /**
     * @FileSizeConstraint()
     * @var File
     */
    public $imageFile;
}

class FileSizeConstraintTest extends KernelTestCase
{

    /**
     * @var FileSizeConstraint
     */
    private $constraint;

    public function setUp()
    {
        self::bootKernel();

        $this->constraint = new FileSizeConstraint();

        parent::setUp();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Validator\FileSizeConstraint::validatedBy
     * @covers \Barthy\ImageUploadBundle\Validator\FileSizeConstraint::getTargets
     * @covers \Barthy\ImageUploadBundle\Validator\FileSizeConstraintValidator::validate
     * @throws \Exception
     */
    public function testValidation()
    {
        $validator = self::$container->get('validator');
        $entity = new FileSizeEntity();

        $entity->imageFile = new File(__DIR__.'/../Fixtures/Files/big_image.jpg', true);
        $violations = $validator->validate($entity);
        $this->assertEquals(1, $violations->count());

        $entity->imageFile = new File(__DIR__.'/../Fixtures/Files/small_image.jpg', true);
        $violations = $validator->validate($entity);
        $this->assertEquals(0, $violations->count());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Validator\FileSizeConstraintValidator::__construct
     * @throws \ReflectionException
     */
    public function testInjection()
    {
        /**
         * @var ImageUploadConfig $config
         */
        $config = self::$container->get(ImageUploadConfig::class);
        $validator = new FileSizeConstraintValidator($config);

        $reflection = new ReflectionClass($validator);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        self::assertEquals($config, $property->getValue($validator));
    }
}
