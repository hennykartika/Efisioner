<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ProfileForm;
use App\Validation\Validator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ProfileFormTest extends TestCase
{
    public function testValidProfilePassesAndIsNormalised(): void
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->passes(), json_encode($validator->errors()) ?: '');
        $values = ProfileForm::values($validator);
        $this->assertSame('ana@example.test', $values['email']);
        $this->assertSame('+6281234567890', $values['phone']);
        $this->assertNull($values['occupation_other']);
    }

    public function testAgeLimits(): void
    {
        $this->assertTrue($this->validate(['birth_date' => '2009-06-01'])->passes());
        $this->assertTrue($this->validate(['birth_date' => '2009-06-02'])->hasError('birth_date'));
        $this->assertTrue($this->validate(['birth_date' => '2030-01-01'])->hasError('birth_date'));
    }

    public function testOtherOccupationNeedsDescription(): void
    {
        $this->assertTrue($this->validate(['occupation' => 'other', 'occupation_other' => ''])->hasError('occupation_other'));

        $validator = $this->validate(['occupation' => 'other', 'occupation_other' => 'Fisherman']);
        $this->assertTrue($validator->passes());
        $this->assertSame('Fisherman', ProfileForm::values($validator)['occupation_other']);
    }

    public function testRejectsUnknownEnumValues(): void
    {
        $validator = $this->validate(['gender' => 'unknown', 'education' => 'phd', 'occupation' => 'admin']);

        $this->assertTrue($validator->hasError('gender'));
        $this->assertTrue($validator->hasError('education'));
        $this->assertTrue($validator->hasError('occupation'));
    }

    /**
     * @param array<string, string> $overrides
     */
    private function validate(array $overrides): Validator
    {
        $input = ProfileForm::normalize([
            'full_name' => 'Ana Putri',
            'email' => ' Ana@Example.test ',
            'phone' => '+62 812-3456-7890',
            'address' => '',
            'birth_date' => '1999-05-17',
            'gender' => 'female',
            'education' => 'bachelor',
            'occupation' => 'student',
            'occupation_other' => 'ignored unless other',
            'institution' => '',
            ...$overrides,
        ]);

        $validator = new Validator($input);
        ProfileForm::validate($validator, new DateTimeImmutable('2026-06-01'));

        return $validator;
    }
}
