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

    /**
     * @covers \BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig::__construct
     */
    public function testConfigInjection()
    {
        self::assertNotNull($this->config);
    }

    /**
     * @covers \BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig::getFileNameLanguage
     */
    public function testFileNameLanguageFunctions()
    {
        self::assertNotNull($this->config->getFileNameLanguage());
    }

    /**
     * @covers \BarthyKoeln\ImageUploadBundle\DependencyInjection\ImageUploadConfig::getMaxFileSize
     */
    public function testMaxFileSizeFunctions()
    {
        self::assertNotNull($this->config->getMaxFileSize());
    }
}
