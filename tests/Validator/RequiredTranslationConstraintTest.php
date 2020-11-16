<?php

namespace Tests\Validator;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use BarthyKoeln\ImageUploadBundle\Validator\RequiredTranslationConstraint;
use BarthyKoeln\ImageUploadBundle\Validator\RequiredTranslationConstraintValidator;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraint;
use Tests\Entity\FileSizeEntity;
use Tests\Entity\SpecificImage;
use Tests\Entity\SpecificImageTranslation;

class RequiredTranslationConstraintTest extends KernelTestCase
{
    private RequiredTranslationConstraint $constraint;

    public function setUp(): void
    {
        self::bootKernel();

        $this->constraint = new RequiredTranslationConstraint();

        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testValidation()
    {
        $validator = self::$container->get('validator');
        $entity    = new SpecificImage();

        $violations = $validator->validate($entity, $this->constraint);
        $this->assertEquals(1, $violations->count());

        $translation = new SpecificImageTranslation();
        $translation->setTitle('something');
        $translation->setLocale('de');

        $entity->addTranslation($translation);
        $violations = $validator->validate($entity, $this->constraint);
        $this->assertEquals(0, $violations->count());

        $violations = $validator->validate(new FileSizeEntity(), $this->constraint);
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
        $validator = new RequiredTranslationConstraintValidator($config);

        $reflection = new ReflectionClass($validator);
        $property   = $reflection->getProperty('config');
        $property->setAccessible(true);

        self::assertEquals($config, $property->getValue($validator));
    }

    public function testTarget()
    {
        self::assertEquals(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
