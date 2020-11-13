<?php

namespace BarthyKoeln\ImageUploadBundle;

use BarthyKoeln\ImageUploadBundle\DependencyInjection\BarthyKoelnImageUploadExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarthyKoelnImageUploadBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BarthyKoelnImageUploadExtension();
    }
}
