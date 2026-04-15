<?php


namespace Stripe\Service\Identity;

class IdentityServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'verificationReports' => VerificationReportService::class,
        'verificationSessions' => VerificationSessionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
