<?php

namespace Ikechukwukalu\Magicmake\Generation;

use InvalidArgumentException;

class GenerationProfile
{
    const LEAN = 'lean';
    const REPOSITORY = 'repository';
    const STANDARD = 'standard';
    const ENTERPRISE = 'enterprise';

    public static function normalize($profile)
    {
        $profile = strtolower(trim((string) $profile));

        if (! in_array($profile, self::all(), true)) {
            throw new InvalidArgumentException('Generation profile must be one of: lean, repository, standard, enterprise.');
        }

        return $profile;
    }

    public static function all()
    {
        return [self::LEAN, self::REPOSITORY, self::STANDARD, self::ENTERPRISE];
    }

    public static function artifacts($profile)
    {
        $profile = self::normalize($profile);

        if ($profile === self::LEAN) {
            return ['model', 'migration', 'factory', 'modelTest'];
        }

        if ($profile === self::REPOSITORY) {
            return ['model', 'migration', 'factory', 'modelTest', 'contract', 'repository', 'service'];
        }

        $artifacts = [
            'model', 'migration', 'contract', 'repository', 'service',
            'controller', 'createRequest', 'updateRequest', 'deleteRequest',
            'readRequest', 'api', 'factory', 'test',
        ];

        if ($profile === self::ENTERPRISE) {
            $artifacts[] = 'provider';
        }

        return $artifacts;
    }
}
