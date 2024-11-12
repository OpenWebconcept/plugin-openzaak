<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use OWC\IdpUserData\DigiDSession;
use OWC\Zaaksysteem\Contracts\BsnResolver;

use function Yard\DigiD\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDBsnResolver implements BsnResolver
{

    public static function make(): self
    {
        return new static();
    }

    public function bsn(): string
    {
        if (DigiDSession::isLoggedIn() && ! is_null(DigiDSession::getUserData())) {
            return DigiDSession::getUserData()->getBsn();
        }

        if (! function_exists('Yard\DigiD\Foundation\Helpers\resolve')) {
            return '';
        }

        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return ! empty($bsn) && is_string($bsn) ? decrypt($bsn) : '';

    }
}
