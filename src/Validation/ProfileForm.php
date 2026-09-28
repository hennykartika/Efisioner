<?php

declare(strict_types=1);

namespace App\Validation;

use App\Domain\Education;
use App\Domain\Gender;
use App\Domain\Occupation;
use DateTimeImmutable;

/**
 * Personal details shared by registration and profile editing.
 */
final class ProfileForm
{
    public const FIELDS = [
        'full_name', 'email', 'phone', 'address', 'birth_date', 'gender',
        'education', 'occupation', 'occupation_other', 'institution',
    ];

    public const MIN_AGE = 17;
    public const MAX_AGE = 100;

    /**
     * @param array<string, string> $input
     * @return array<string, string>
     */
    public static function normalize(array $input): array
    {
        $input['email'] = mb_strtolower(trim($input['email'] ?? ''));
        $input['phone'] = (string) preg_replace('/[\s().-]/', '', $input['phone'] ?? '');

        return $input;
    }

    public static function validate(Validator $validator, DateTimeImmutable $today): void
    {
        $validator
            ->required('full_name', 'Full name')
            ->length('full_name', 'Full name', 2, 100)
            ->required('email', 'Email')
            ->length('email', 'Email', 3, 254)
            ->email('email', 'Email')
            ->pattern('phone', '/^\+?[0-9]{8,15}$/', 'Phone number must have 8 to 15 digits and may start with +.')
            ->length('address', 'Address', 0, 500)
            ->required('birth_date', 'Date of birth')
            ->date('birth_date', 'Date of birth')
            ->required('gender', 'Gender')
            ->enum('gender', 'Gender', Gender::class)
            ->required('education', 'Education')
            ->enum('education', 'Education', Education::class)
            ->required('occupation', 'Occupation')
            ->enum('occupation', 'Occupation', Occupation::class)
            ->length('institution', 'Institution', 0, 150);

        if ($validator->value('occupation') === Occupation::Other->value) {
            $validator
                ->required('occupation_other', 'Occupation')
                ->length('occupation_other', 'Occupation', 2, 100);
        }

        if (!$validator->hasError('birth_date')) {
            $age = (new DateTimeImmutable($validator->value('birth_date')))->diff($today);
            $years = $age->invert === 0 ? $age->y : -1;

            if ($years < self::MIN_AGE || $years > self::MAX_AGE) {
                $validator->addError('birth_date', sprintf('You must be between %d and %d years old.', self::MIN_AGE, self::MAX_AGE));
            }
        }
    }

    /**
     * @return array<string, string|null>
     */
    public static function values(Validator $validator): array
    {
        $isOther = $validator->value('occupation') === Occupation::Other->value;

        return [
            'full_name' => $validator->value('full_name'),
            'email' => $validator->value('email'),
            'phone' => $validator->nullable('phone'),
            'address' => $validator->nullable('address'),
            'birth_date' => $validator->value('birth_date'),
            'gender' => $validator->value('gender'),
            'education' => $validator->value('education'),
            'occupation' => $validator->value('occupation'),
            'occupation_other' => $isOther ? $validator->nullable('occupation_other') : null,
            'institution' => $validator->nullable('institution'),
        ];
    }
}
