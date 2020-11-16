<?php

namespace Tests\DependencyInjection;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageUploadConfigTest extends KernelTestCase
{
    protected ?ImageUploadConfig $config;

    public function setUp(): void
    {
        self::bootKernel();

        $this->config = self::$container->get(ImageUploadConfig::class);

        parent::setUp();
    }

    public function testConfigInjection()
    {
        self::assertNotNull($this->config);
    }

    public function testRequiredTranslationFunctions()
    {
        self::assertNotNull($this->config->getRequiredTranslation());
    }

    public function testMaxFileSizeFunctions()
    {
        self::assertNotNull($this->config->getMaxFileSize());
    }
}
