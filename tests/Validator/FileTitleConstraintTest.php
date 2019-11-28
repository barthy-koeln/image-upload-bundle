<?php

namespace Barthy\ImageUploadBundle\Test\Validator;

use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Barthy\ImageUploadBundle\Entity\ImageTranslationTrait;
use Barthy\ImageUploadBundle\Tests\Entity\SpecificImage;
use Barthy\ImageUploadBundle\Tests\Entity\SpecificImageTranslation;
use Barthy\ImageUploadBundle\Validator\FileTitleConstraint;
use Barthy\ImageUploadBundle\Validator\FileTitleConstraintValidator;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraint;

class FileTitleConstraintTest extends KernelTestCase
{

    /**
     * @var FileTitleConstraint
     */
    private $constraint;

    public function setUp()
    {
        self::bootKernel();

        $this->constraint = new FileTitleConstraint();

        parent::setUp();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Validator\FileTitleConstraint::validatedBy
     * @covers \Barthy\ImageUploadBundle\Validator\FileTitleConstraint::getTargets
     * @covers \Barthy\ImageUploadBundle\Validator\FileTitleConstraintValidator::validate
     * @throws \Exception
     */
    public function testValidation()
    {
        $validator = self::$container->get('validator');
        $entity = new SpecificImage();

        $violations = $validator->validate($entity, $this->constraint);
        $this->assertEquals(1, $violations->count());

        /**
         * @var  ImageTranslationTrait $translation
         */
        $translation = new SpecificImageTranslation();
        $translation->setTitle('something');
        $translation->setLocale('de');

        $entity->addTranslation($translation);
        $violations = $validator->validate($entity, $this->constraint);

        $this->assertEquals(0, $violations->count());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Validator\FileTitleConstraintValidator::__construct
     * @throws \ReflectionException
     */
    public function testInjection()
    {
        /**
         * @var ImageUploadConfig $config
         */
        $config = self::$container->get(ImageUploadConfig::class);
        $validator = new FileTitleConstraintValidator($config);

        $reflection = new ReflectionClass($validator);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        self::assertEquals($config, $property->getValue($validator));
    }

    public function testTarget()
    {
        self::assertEquals(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
