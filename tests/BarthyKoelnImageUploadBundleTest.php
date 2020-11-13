<?php

namespace Tests;

use BarthyKoeln\ImageUploadBundle\BarthyKoelnImageUploadBundle;
use BarthyKoeln\ImageUploadBundle\DependencyInjection\BarthyKoelnImageUploadExtension;
use PHPUnit\Framework\TestCase;

class BarthyKoelnImageUploadBundleTest extends TestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new BarthyKoelnImageUploadBundle();
        self::assertInstanceOf(BarthyKoelnImageUploadExtension::class, $bundle->getContainerExtension());
    }
}
