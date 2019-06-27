<?php 
namespace Mathiastools\TracyExtensions\Profiler;

class Profiler extends AdvancedProfiler
{
    /**
     * @inheritdoc
     */
    public static function enable($realUsage = false)
    {
        /** @noinspection PhpInternalEntityUsedInspection */
        ProfilerService::init();
        parent::enable($realUsage);
    }
}