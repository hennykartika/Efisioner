<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Gender;
use App\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testTrimsAndStripsControlCharacters(): void
    {
        $validator = new Validator(['name' => "  Ana\x00\x1B Maria  ", 'bio' => "line one\nline two"]);

        $this->assertSame('Ana Maria', $validator->value('name'));
        $this->assertSame("line one\nline two", $validator->value('bio'));
    }

    public function testRequiredAndLength(): void
    {
        $validator = (new Validator(['a' => '', 'b' => 'xy', 'c' => str_repeat('z', 11)]))
            ->required('a', 'A')
            ->length('b', 'B', 3, 10)
            ->length('c', 'C', 3, 10);

        $this->assertFalse($validator->passes());
        $this->assertSame('A is required.', $validator->errors()['a']);
        $this->assertSame('B must be at least 3 characters.', $validator->errors()['b']);
        $this->assertSame('C must be at most 10 characters.', $validator->errors()['c']);
    }

    public function testLengthCountsCharactersNotBytes(): void
    {
        $validator = (new Validator(['name' => 'Ñandú']))->length('name', 'Name', 5, 5);

        $this->assertTrue($validator->passes());
    }

    public function testIntegerRejectsDecimalsNegativesAndOutOfRange(): void
    {
        foreach (['1.5', '-10', '1e3', '0x10', '1000001', 'abc'] as $input) {
            $validator = (new Validator(['n' => $input]))->integer('n', 'N', 1000, 1000000);
            $this->assertFalse($validator->passes(), "Expected '{$input}' to be rejected");
        }

        $this->assertTrue((new Validator(['n' => '5000']))->integer('n', 'N', 1000, 1000000)->passes());
    }

    public function testEnumAcceptsOnlyKnownValues(): void
    {
        $this->assertTrue((new Validator(['g' => 'female']))->enum('g', 'Gender', Gender::class)->passes());
        $this->assertFalse((new Validator(['g' => 'admin']))->enum('g', 'Gender', Gender::class)->passes());
    }

    public function testDateMustBeRealCalendarDate(): void
    {
        $this->assertTrue((new Validator(['d' => '2000-02-29']))->date('d', 'Date')->passes());
        $this->assertFalse((new Validator(['d' => '2001-02-29']))->date('d', 'Date')->passes());
        $this->assertFalse((new Validator(['d' => '17/05/1999']))->date('d', 'Date')->passes());
    }

    public function testKeepsFirstErrorPerField(): void
    {
        $validator = (new Validator(['e' => '']))->required('e', 'Email')->email('e', 'Email');
        $validator->addError('e', 'Second message');

        $this->assertSame('Email is required.', $validator->errors()['e']);
    }
}
