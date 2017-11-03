<?php
/**
 * This file is part of the prooph/message-flow-analyzer.
 * (c) 2017-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\MessageFlowAnalyzer\Output;

use Prooph\MessageFlowAnalyzer\Helper\Util;
use Prooph\MessageFlowAnalyzer\MessageFlow;

final class JsonArangoGraphNodes implements Formatter
{
    public function messageFlowToString(MessageFlow $messageFlow): string
    {
        $messages = [];
        $handlers = [];
        $edges = [];

        foreach ($messageFlow->messages() as $message) {
            $msgKey = Util::identifierToKey($message->name());
            $messages[$msgKey] = [
                '_key' => $msgKey,
                'type' => $message->type(),
                'name' => Util::withoutNamespace($message->name()),
                'class' => $message->class()
            ];

            foreach ($message->handlers() as $handler) {
                $handlerKey = Util::identifierToKey($handler->identifier());
                $handlers[$handlerKey] = [
                    '_key' => $handlerKey,
                    'name' => $handler->isClass()? Util::withoutNamespace($handler->class()) : $handler->function(),
                    'class' => $handler->class(),
                    'function' => $handler->function(),
                ];

                $edges[] = [
                    'from' => 'messages/'.$msgKey,
                    'to' => 'handlers/'.$handlerKey
                ];
            }

            foreach ($message->producers() as $producer) {
                $producerKey = Util::identifierToKey($producer->identifier());
                $handlers[$producerKey] = [
                    '_key' => $producerKey,
                    'name' => $producer->isClass()? Util::withoutNamespace($producer->class()) : $producer->function(),
                    'class' => $producer->class(),
                    'function' => $producer->function(),
                ];

                $edges[] = [
                    'from' => 'handlers/'.$producerKey,
                    'to' => 'messages/'.$msgKey
                ];
            }

            foreach ($message->recorders() as $recorder) {
                $recorderKey = Util::identifierToKey($recorder->identifier());
                $handlers[$recorderKey] = [
                    '_key' => $recorderKey,
                    'name' => $recorder->isClass()? Util::withoutNamespace($recorder->class()) : $recorder->function(),
                    'class' => $recorder->class(),
                    'function' => $recorder->function(),
                ];

                $edges[] = [
                    'from' => 'handlers/'.$recorderKey,
                    'to' => 'messages/'.$msgKey,
                ];
            }
        }

        foreach ($messageFlow->eventRecorderInvokers() as $eventRecorderInvoker) {
            $edges[] = [
                'from' => 'handlers/'.Util::identifierToKey($eventRecorderInvoker->messageHandlerIdentifier()),
                'to' => 'handlers/'.Util::identifierToKey($eventRecorderInvoker->eventRecorderIdentifier())
            ];
        }

        return json_encode([
            'messages' => $messages,
            'handlers' => $handlers,
            'edges' => $edges
        ], JSON_PRETTY_PRINT);
    }
}