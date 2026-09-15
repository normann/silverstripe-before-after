<?php

namespace Normann\BeforeAfter\Tests\Model;

use Normann\BeforeAfter\Model\BeforeAfterImage;
use SilverStripe\Dev\SapphireTest;

class BeforeAfterImageTest extends SapphireTest
{
    public function testDefaultsApplyToNewRecords(): void
    {
        $image = BeforeAfterImage::create();

        $this->assertTrue((bool) $image->HideTitle);
        $this->assertSame(50, $image->SliderDefaultOffset);
        $this->assertSame('Before', $image->BeforeLabel);
        $this->assertSame('After', $image->AfterLabel);
        $this->assertTrue((bool) $image->ShowBadge);
    }

    public function testGetRotationDegreesFallsBackWhenNoAfterImage(): void
    {
        $image = BeforeAfterImage::create();

        $this->assertSame(56.12, $image->getRotationDegrees());
    }

    public function testDiagonalHandlerStyleIsEmptyForNonDiagonalDirections(): void
    {
        $image = BeforeAfterImage::create();
        $image->SliderDirection = 'horizontal';

        $this->assertSame('', $image->getDiagonalHandlerStyle());
    }

    public function testImageCaptionsJsonFallsBackToDefaults(): void
    {
        $image = BeforeAfterImage::create();
        $image->BeforeImageCaption = null;
        $image->AfterImageCaption = null;

        $decoded = json_decode($image->getImageCaptionsJson(), true);

        $this->assertSame('Before Image Caption', $decoded['before']);
        $this->assertSame('After Image Caption', $decoded['after']);
    }
}
