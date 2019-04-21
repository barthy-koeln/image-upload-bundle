<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 15.08.18
 * Time: 08:51
 */

namespace Barthy\ImageUploadBundle\Twig;


use Barthy\ImageUploadBundle\Entity\Image;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageFilterExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('thumbnailParams', [$this, 'thumbnailParams']),
        );
    }

    public function thumbnailParams(Image $image, int $w, int $h, string $mode = 'outbound')
    {
        $params = [
            'crop' => [
                'size' => [
                    $image->getW(),
                    $image->getH(),
                ],
                'start' => [
                    $image->getX(),
                    $image->getY(),
                ],
            ],
            'thumbnail' => [
                'size' => [
                    $w,
                    $h,
                ],
                'mode' => $mode,
            ],
        ];

        return $params;
    }

}