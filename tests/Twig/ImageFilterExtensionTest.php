<?php

namespace Tests\Twig;

use BarthyKoeln\ImageUploadBundle\Twig\ImageFilterExtension;
use PHPUnit\Framework\TestCase;
use Tests\Entity\SpecificImage;

class ImageFilterExtensionTest extends TestCase
{
    private ImageFilterExtension $extension;

    public function setUp(): void
    {
        $this->extension = new ImageFilterExtension();

        parent::setUp();
    }

    public function testFilterDeclaration()
    {
        self::assertGreaterThan(0, count($this->extension->getFilters()));
    }

    public function testThumbnailParamsFilter()
    {
        $image = new SpecificImage();
        $image->setX(10);
        $image->setY(20);
        $image->setW(500);
        $image->setH(600);

        $params = [
            'crop'      => [
                'size'  => [
                    500,
                    600,
                ],
                'start' => [
                    10,
                    20,
                ],
            ],
            'thumbnail' => [
                'size' => [
                    200,
                    300,
                ],
                'mode' => 'outbound',
            ],
        ];

        $this->assertEquals($params, $this->extension->thumbnailParams($image, 200, 300));

        $params['thumbnail']['mode'] = 'inset';
        $this->assertEquals($params, $this->extension->thumbnailParams($image, 200, 300, 'inset'));
    }
}
