<?php

namespace BarthyKoeln\ImageUploadBundle\Twig;

use BarthyKoeln\ImageUploadBundle\Entity\ImageInterface;
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

        $values = [
            $image->getW(),
            $image->getH(),
            $image->getX(),
            $image->getY(),
        ];

        $crop = 4 === count(array_filter($values));

        if ($crop && null !== $image) {
            $params = [
                'crop' => [
                    'size'  => [
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
