<?php

declare(strict_types=1);

namespace Phpolar\PHpolar\Http;

use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\Phpolar\Http\Representations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(Representations::class)]
final class RepresentationsTest extends TestCase
{
    #[TestDox("Shall know if it contains an acceptable representations")]
    #[TestWith([[MimeType::ApplicationEpubZip, MimeType::ApplicationGzip], [MimeType::ApplicationGzip->value, MimeType::ApplicationRtf->value]])]
    public function testa(array $representations, array $acceptableRepresentations)
    {
        $sut = new Representations($representations);

        $result = $sut->contains($acceptableRepresentations);

        $this->assertTrue($result);
    }

    #[TestDox("Shall know if it contains an acceptable representations")]
    #[TestWith([[MimeType::ApplicationEpubZip, MimeType::ApplicationGzip], [MimeType::ApplicationJson->value, MimeType::ApplicationRtf->value]])]
    public function testb(array $representations, array $acceptableRepresentations)
    {
        $sut = new Representations($representations);

        $result = $sut->contains($acceptableRepresentations);

        $this->assertFalse($result);
    }
}
