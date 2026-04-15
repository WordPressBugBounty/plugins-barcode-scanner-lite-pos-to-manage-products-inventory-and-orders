<?php


namespace Stripe\Service\Reporting;

class ReportingServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'reportRuns' => ReportRunService::class,
        'reportTypes' => ReportTypeService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
