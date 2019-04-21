<?php

use Barthy\ImageUploadBundle\BarthyImageUploadBundle;
use Barthy\ImageUploadBundle\DependencyInjection\BarthyImageUploadExtension;
use PHPUnit\Framework\TestCase;

class BarthyImageUploadBundleTest extends TestCase
{

    public function testGetContainerExtension()
    {
        $bundle = new BarthyImageUploadBundle();
        self::assertInstanceOf(BarthyImageUploadExtension::class, $bundle->getContainerExtension());
    }
}
