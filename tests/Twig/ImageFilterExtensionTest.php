<?php

namespace Barthy\ImageUploadBundle\Test\Twig;

use Barthy\ImageUploadBundle\Entity\Image;
use Barthy\ImageUploadBundle\Twig\ImageFilterExtension;
use PHPUnit\Framework\TestCase;

class ImageFilterExtensionTest extends TestCase
{

    /**
     * @var ImageFilterExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new ImageFilterExtension();

        parent::setUp();
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Twig\ImageFilterExtension::getFilters
     */
    public function testFilterDeclaration()
    {
        self::assertGreaterThan(0, count($this->extension->getFilters()));
    }

    /**
     * @covers \Barthy\ImageUploadBundle\Twig\ImageFilterExtension::thumbnailParams
     */
    public function testThumbnailParamsFilter()
    {
        $image = new Image();
        $image->setX(10);
        $image->setY(20);
        $image->setW(500);
        $image->setH(600);

        $params = [
            'crop' => [
                'size' => [
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
