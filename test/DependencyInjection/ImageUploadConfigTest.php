<?php

namespace Barthy\ImageUploadBundle\Test\DependencyInjection;

use Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageUploadConfigTest extends KernelTestCase
{

    /**
     * @var ImageUploadConfig
     */
    protected $config;

    public function setUp()
    {
        self::bootKernel();

        $this->config = self::$container->get(ImageUploadConfig::class);

        parent::setUp();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig::__construct
     */
    public function testConfigInjection()
    {
        self::assertNotNull($this->config);
    }

    /**
     * @covers \Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig::getFileNameLanguage
     */
    public function testFileNameLanguageFunctions()
    {
        self::assertNotNull($this->config->getFileNameLanguage());
    }

    /**
     * @covers \Barthy\ImageUploadBundle\DependencyInjection\ImageUploadConfig::getMaxFileSize
     */
    public function testMaxFileSizeFunctions()
    {
        self::assertNotNull($this->config->getMaxFileSize());
    }
}
