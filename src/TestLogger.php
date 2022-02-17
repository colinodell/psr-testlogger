<?php

declare(strict_types=1);

namespace ColinODell\PsrTestLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Used for testing purposes.
 *
 * It records all records and gives you access to them for verification.
 *
 * @method bool hasEmergency($record)
 * @method bool hasAlert($record)
 * @method bool hasCritical($record)
 * @method bool hasError($record)
 * @method bool hasWarning($record)
 * @method bool hasNotice($record)
 * @method bool hasInfo($record)
 * @method bool hasDebug($record)
 * @method bool hasEmergencyRecords()
 * @method bool hasAlertRecords()
 * @method bool hasCriticalRecords()
 * @method bool hasErrorRecords()
 * @method bool hasWarningRecords()
 * @method bool hasNoticeRecords()
 * @method bool hasInfoRecords()
 * @method bool hasDebugRecords()
 * @method bool hasEmergencyThatContains($message)
 * @method bool hasAlertThatContains($message)
 * @method bool hasCriticalThatContains($message)
 * @method bool hasErrorThatContains($message)
 * @method bool hasWarningThatContains($message)
 * @method bool hasNoticeThatContains($message)
 * @method bool hasInfoThatContains($message)
 * @method bool hasDebugThatContains($message)
 * @method bool hasEmergencyThatMatches($message)
 * @method bool hasAlertThatMatches($message)
 * @method bool hasCriticalThatMatches($message)
 * @method bool hasErrorThatMatches($message)
 * @method bool hasWarningThatMatches($message)
 * @method bool hasNoticeThatMatches($message)
 * @method bool hasInfoThatMatches($message)
 * @method bool hasDebugThatMatches($message)
 * @method bool hasEmergencyThatPasses($message)
 * @method bool hasAlertThatPasses($message)
 * @method bool hasCriticalThatPasses($message)
 * @method bool hasErrorThatPasses($message)
 * @method bool hasWarningThatPasses($message)
 * @method bool hasNoticeThatPasses($message)
 * @method bool hasInfoThatPasses($message)
 * @method bool hasDebugThatPasses($message)
 *
 * Adapted from psr/log,
 * Copyright (c) 2012 PHP Framework Interoperability Group
 * Used under the MIT license
 */
final class TestLogger extends AbstractLogger
{
    /** @var array<int, array<string, mixed>> */
    public array $records = [];

    /** @var array<int|string, array<int, array<string, mixed>>> */
    public array $recordsByLevel = [];

    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed> $context
     */
    public function log($level, $message, array $context = []): void
    {
        $record = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        $this->recordsByLevel[$record['level']][] = $record;
        $this->records[]                          = $record;
    }

    /**
     * @param LogLevel::* $level
     */
    public function hasRecords(string $level): bool
    {
        return isset($this->recordsByLevel[$level]);
    }

    /**
     * @param string|array<string, mixed> $record
     * @param LogLevel::*                 $level
     */
    public function hasRecord($record, string $level): bool
    {
        if (\is_string($record)) {
            $record = ['message' => $record];
        }

        return $this->hasRecordThatPasses(static function (array $rec) use ($record) {
            if ($rec['message'] !== $record['message']) {
                return false;
            }

            return ! isset($record['context']) || $rec['context'] === $record['context'];
        }, $level);
    }

    /**
     * @param LogLevel::* $level
     */
    public function hasRecordThatContains(string $message, string $level): bool
    {
        return $this->hasRecordThatPasses(static function (array $rec) use ($message) {
            return \strpos($rec['message'], $message) !== false;
        }, $level);
    }

    /**
     * @param LogLevel::* $level
     */
    public function hasRecordThatMatches(string $regex, string $level): bool
    {
        return $this->hasRecordThatPasses(static function ($rec) use ($regex) {
            return \preg_match($regex, $rec['message']) > 0;
        }, $level);
    }

    /**
     * @param callable(array<string, mixed>, int): bool $predicate
     * @param LogLevel::*                               $level
     */
    public function hasRecordThatPasses(callable $predicate, $level): bool
    {
        if (! isset($this->recordsByLevel[$level])) {
            return false;
        }

        foreach ($this->recordsByLevel[$level] as $i => $rec) {
            if (\call_user_func($predicate, $rec, $i)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, mixed> $args
     */
    public function __call(string $method, array $args): bool
    {
        if (\preg_match('/(.*)(Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency)(.*)/', $method, $matches) > 0) {
            $genericMethod = $matches[1] . ($matches[3] !== 'Records' ? 'Record' : '') . $matches[3];
            $callable      = [$this, $genericMethod];
            $level         = \strtolower($matches[2]);
            if (\is_callable($callable)) {
                $args[] = $level;

                return \call_user_func_array($callable, $args);
            }
        }

        throw new \BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
    }

    public function reset(): void
    {
        $this->records        = [];
        $this->recordsByLevel = [];
    }
}
