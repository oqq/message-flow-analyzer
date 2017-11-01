<?php
/**
 * This file is part of the prooph/message-flow-analyzer.
 * (c) 2017-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\MessageFlowAnalyzer\Helper;

use Prooph\MessageFlowAnalyzer\Filter\ExcludeHiddenFileInfo;
use Prooph\MessageFlowAnalyzer\Filter\ExcludeVendorDir;
use Prooph\MessageFlowAnalyzer\Filter\IncludePHPFile;
use Prooph\MessageFlowAnalyzer\ProjectTraverser;
use Prooph\MessageFlowAnalyzer\Visitor\EventRecorderCollector;
use Prooph\MessageFlowAnalyzer\Visitor\MessageCollector;
use Prooph\MessageFlowAnalyzer\Visitor\MessageHandlerCollector;
use Prooph\MessageFlowAnalyzer\Visitor\MessageProducerCollector;

final class ProjectTraverserFactory
{
    public static $filterAliases = [
        'ExcludeVendorDir' => ExcludeVendorDir::class,
        'ExcludeHiddenFileInfo' => ExcludeHiddenFileInfo::class,
        'IncludePHPFile' => IncludePHPFile::class,
    ];

    public static $classVisitorAliases = [
        'MessageCollector' => MessageCollector::class,
        'MessageHandlerCollector' => MessageHandlerCollector::class,
        'MessageProducerCollector' => MessageProducerCollector::class,
        'EventRecorderCollector' => EventRecorderCollector::class,
    ];

    public static $fileInfoVisitorAliases = [];

    public static function buildTraverserFromConfig(array $config): ProjectTraverser
    {
        if(!array_key_exists('name', $config)) {
            throw new \InvalidArgumentException("Missing project name in configuration");
        }

        $traverser = new ProjectTraverser($config['name']);

        foreach ($config['fileInfoFilters'] ?? [] as $filterClass) {
            $filterClass = self::$filterAliases[$filterClass] ?? $filterClass;
            $traverser->addFileInfoFilter(new $filterClass);
        }

        foreach ($config['classVisitors'] ?? [] as $classVisitorClass) {
            $classVisitorClass = self::$classVisitorAliases[$classVisitorClass] ?? $classVisitorClass;
            $traverser->addClassVisitor(new $classVisitorClass);
        }

        foreach ($config['fileInfoVisitors'] ?? [] as $fileInfoVisitorClass) {
            $fileInfoVisitorClass = self::$fileInfoVisitorAliases[$fileInfoVisitorClass] ?? $fileInfoVisitorClass;
            $traverser->addFileInfoVisitor(new $fileInfoVisitorClass);
        }

        return $traverser;
    }
}