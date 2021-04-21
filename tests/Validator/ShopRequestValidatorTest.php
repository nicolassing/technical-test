<?php

namespace App\Tests\Validator;

use App\Validator\ShopRequestValidator;
use PHPUnit\Framework\TestCase;

class ShopRequestValidatorTest extends TestCase
{
    public function testValidationOk(): void
    {
        $validator = new ShopRequestValidator();
        $request = [
            'lat' => 1.0,
            'lon' => 2.0,
            'distance' => 10,
            'q' => 'sezane',
        ];
        self::assertCount(0, $validator->validate($request));
    }

    public function testValidationKO(): void
    {
        $validator = new ShopRequestValidator();
        $request = [
            'lat' => '1',
            'lon' => '2',
            'distance' => 10,
            'q' => 'sezane',
        ];
        self::assertCount(2, $validator->validate($request));
    }
}
