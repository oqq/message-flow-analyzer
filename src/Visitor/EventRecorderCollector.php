<?php
/**
 * This file is part of the prooph/message-flow-analyzer.
 * (c) 2017-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\MessageFlowAnalyzer\Visitor;

use Prooph\MessageFlowAnalyzer\Helper\MessageProducingMethodScanner;
use Prooph\MessageFlowAnalyzer\MessageFlow;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class EventRecorderCollector implements ClassVisitor
{
    use MessageProducingMethodScanner;

    public function onClassReflection(ReflectionClass $reflectionClass, MessageFlow $messageFlow): MessageFlow
    {
        if(!MessageFlow\EventRecorder::isEventRecorder($reflectionClass)) {
            return $messageFlow;
        }

        return $this->checkMessageProduction(
            $reflectionClass,
            function (MessageFlow\Message $message, ReflectionMethod $method): MessageFlow\Message {
                return $message->addRecorder(MessageFlow\EventRecorder::fromReflectionMethod($method));
            },
            $messageFlow);
    }
}
