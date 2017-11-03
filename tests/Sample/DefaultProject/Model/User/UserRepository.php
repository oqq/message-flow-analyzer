<?php
/**
 * This file is part of the prooph/message-flow-analyzer.
 * (c) 2017-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProophTest\MessageFlowAnalyzer\Sample\DefaultProject\Model\User;

use ProophTest\MessageFlowAnalyzer\Sample\DefaultProject\Model\User;

interface UserRepository
{
    public function get(string $userId): User;

    public function add(User $user): void;

    public function save(User $user): void;
}