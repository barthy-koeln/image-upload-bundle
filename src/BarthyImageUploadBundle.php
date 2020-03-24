<?php
/**
 * Created by PhpStorm.
 * User: bbonhomme
 * Date: 16.10.18
 * Time: 17:59
 */

namespace Barthy\ImageUploadBundle;

use Barthy\ImageUploadBundle\DependencyInjection\BarthyImageUploadExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarthyImageUploadBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new BarthyImageUploadExtension();
    }
}
