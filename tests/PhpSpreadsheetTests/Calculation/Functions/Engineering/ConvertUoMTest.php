<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

class ConvertUoMTest extends TestCase
{
    const UOM_PRECISION = 1E-12;

    public function testGetConversionGroups(): void
    {
        $result = Engineering::getConversionGroups();
        self::assertIsArray($result);
    }

    public function testGetConversionGroupUnits(): void
    {
        $result = Engineering::getConversionGroupUnits();
        self::assertIsArray($result);
    }

    public function testGetConversionGroupUnitDetails(): void
    {
        $result = Engineering::getConversionGroupUnitDetails();
        self::assertIsArray($result);
    }

    public function testGetConversionMultipliers(): void
    {
        $result = Engineering::getConversionMultipliers();
        self::assertIsArray($result);
    }

    public function testGetBinaryConversionMultipliers(): void
    {
        $result = Engineering::getBinaryConversionMultipliers();
        self::assertIsArray($result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     *
     * @param mixed $expectedResult
     */
    public function testCONVERTUOM($expectedResult, ...$args): void
    {
        $result = Engineering::CONVERTUOM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);
    }

    public function providerCONVERTUOM(): array
    {
        return require 'tests/data/Calculation/Engineering/CONVERTUOM.php';
    }

    /**
     * @dataProvider providerConvertUoMArray
     */
    public function testConvertUoMArray(array $expectedResult, string $value, string $fromUoM, string $toUoM): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CONVERT({$value}, {$fromUoM}, {$toUoM})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);
    }

    public function providerConvertUoMArray(): array
    {
        return [
            'Weight/Mass' => [
                [
                    [71.42857142857142, 0.15747304441777],
                    [453.5923699999991, 1.0],
                ],
                '1000',
                '{"lbm", "g"}',
                '{"stone"; "kg"}',
            ],
            'Distance' => [
                [
                    [2025371.8285214372, 1093.6132983377101],
                    [1851.9999999999984, 1.0],
                ],
                '1000',
                '{"Nmi", "m"}',
                '{"yd"; "km"}',
            ],
            'Volume' => [
                [
                    [2.976190476190475, 0.00628981077043211],
                    [473.1764729999994, 1.0],
                ],
                '1000',
                '{"pt", "ml"}',
                '{"barrel"; "l"}',
            ],
            'Area' => [
                [
                    [999.9960000040016, 0.247104393046628],
                    [404.6856422400005, 0.1],
                ],
                '1000',
                '{"uk_acre", "m2"}',
                '{"us_acre"; "ha"}',
            ],
        ];
    }
}
