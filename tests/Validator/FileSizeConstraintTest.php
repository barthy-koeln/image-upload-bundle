<?php

namespace Tests\Validator;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Validator\FileSizeConstraint;
use BarthyKoeln\ImageUploadBundle\Validator\FileSizeConstraintValidator;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Entity\FileSizeEntity;

class FileSizeConstraintTest extends KernelTestCase
{
    private FileSizeConstraint $constraint;
    private ?ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();

        $this->constraint = new FileSizeConstraint();
        $this->validator  = self::$container->get('validator');

        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testValidation()
    {
        $entity    = new FileSizeEntity();

        $entity->imageFile = new File(__DIR__.'/../Fixtures/Files/big_image.jpg', true);
        $violations        = $this->validator->validate($entity->imageFile, $this->constraint);
        $this->assertEquals(1, $violations->count());

        $entity->imageFile = new File(__DIR__.'/../Fixtures/Files/small_image.jpg', true);
        $violations        = $this->validator->validate($entity->imageFile, $this->constraint);
        $this->assertEquals(0, $violations->count());
    }

    /**
     * @throws \ReflectionException
     */
    public function testInjection()
    {
        /**
         * @var ImageUploadConfig $config
         */
        $config    = self::$container->get(ImageUploadConfig::class);
        $validator = new FileSizeConstraintValidator($config);

        $reflection = new ReflectionClass($validator);
        $property   = $reflection->getProperty('config');
        $property->setAccessible(true);

        self::assertEquals($config, $property->getValue($validator));
    }
}
