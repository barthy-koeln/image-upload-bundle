<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 15.08.18
 * Time: 08:51
 */

namespace Barthy\ImageUploadBundle\Twig;


use Barthy\ImageUploadBundle\Entity\ImageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageFilterExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('thumbnailParams', [$this, 'thumbnailParams']),
        ];
    }

    public function thumbnailParams(
        ?ImageInterface $image,
        ?int $width = null,
        ?int $height = null,
        string $mode = 'outbound'
    ): array {
        $params = [];

        if ($image !== null) {
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
            ];
        }

        if (null !== $width && null !== $height) {
            $params['thumbnail'] = [
                'size' => [
                    $width,
                    $height,
                ],
                'mode' => $mode,
            ];
        }

        return $params;
    }

}
