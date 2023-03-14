<?php

declare(strict_types=1);

namespace ColinODell\PsrTestLogger\Tests\Unit;

use ColinODell\PsrTestLogger\TestLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

final class TestLoggerTest extends TestCase
{
    /**
     * @dataProvider provideLogLevels
     */
    public function testHasRecords(string $level): void
    {
        $magicMethod = 'has' . \ucfirst($level) . 'Records';

        $logger = new TestLogger();
        $this->assertFalse($logger->hasRecords($level));
        $this->assertFalse($logger->$magicMethod());

        $logger->log($level, 'Test');
        $this->assertTrue($logger->hasRecords($level));
        $this->assertTrue($logger->$magicMethod());
    }

    /**
     * @dataProvider provideLogLevels
     */
    public function testHasRecord(string $level): void
    {
        $magicMethod = 'has' . \ucfirst($level);

        $logger = new TestLogger();
        $this->assertFalse($logger->hasRecord('Test', $level));
        $this->assertFalse($logger->hasRecord(['message' => 'Test'], $level));
        $this->assertFalse($logger->$magicMethod('Test'));
        $this->assertFalse($logger->$magicMethod(['message' => 'Test']));

        $logger->log($level, 'Test');

        $this->assertTrue($logger->hasRecord('Test', $level));
        $this->assertTrue($logger->hasRecord(['message' => 'Test'], $level));
        $this->assertTrue($logger->$magicMethod('Test'));
        $this->assertTrue($logger->$magicMethod(['message' => 'Test']));

        $this->assertFalse($logger->hasRecord('Some message we have not logged', $level));
        $this->assertFalse($logger->hasRecord(['message' => 'Some message we have not logged'], $level));
        $this->assertFalse($logger->$magicMethod('Some message we have not logged'));
        $this->assertFalse($logger->$magicMethod(['message' => 'Some message we have not logged']));
    }

    /**
     * @dataProvider provideLogLevels
     */
    public function testHasRecordThatContains(string $level): void
    {
        $magicMethod = 'has' . \ucfirst($level) . 'ThatContains';

        $logger = new TestLogger();
        $this->assertFalse($logger->hasRecordThatContains('Test', $level));
        $this->assertFalse($logger->$magicMethod('Test'));

        $logger->log($level, 'This Is A Test');

        $this->assertTrue($logger->hasRecordThatContains('Test', $level));
        $this->assertTrue($logger->$magicMethod('Test'));
    }

    /**
     * @dataProvider provideLogLevels
     */
    public function testHasRecordThatMatches(string $level): void
    {
        $magicMethod = 'has' . \ucfirst($level) . 'ThatMatches';

        $logger = new TestLogger();
        $this->assertFalse($logger->hasRecordThatMatches('/test/i', $level));
        $this->assertFalse($logger->$magicMethod('/test/i'));

        $logger->log($level, 'This Is A Test');

        $this->assertTrue($logger->hasRecordThatMatches('/test/i', $level));
        $this->assertTrue($logger->$magicMethod('/test/i'));
    }

    /**
     * @dataProvider provideLogLevels
     */
    public function testHasRecordThatPasses(string $level): void
    {
        $magicMethod = 'has' . \ucfirst($level) . 'ThatPasses';

        $logger = new TestLogger();
        $this->assertFalse($logger->hasRecordThatPasses(static function ($record) {
            return $record['message'] === 'Test';
        }, $level));
        $this->assertFalse($logger->$magicMethod(static function ($record) {
            return $record['message'] === 'Test';
        }));

        $logger->log($level, 'Test');

        $this->assertTrue($logger->hasRecordThatPasses(static function ($record) {
            return $record['message'] === 'Test';
        }, $level));
        $this->assertTrue($logger->$magicMethod(static function ($record) {
            return $record['message'] === 'Test';
        }));

        $this->assertFalse($logger->hasRecordThatPasses(static function ($record) {
            return $record['message'] === 'Some message we have not logged';
        }, $level));
    }

    public function testReset(): void
    {
        $logger = new TestLogger();

        $logger->log(LogLevel::DEBUG, 'Test');
        $this->assertTrue($logger->hasRecords(LogLevel::DEBUG));

        $logger->reset();
        $this->assertFalse($logger->hasRecords(LogLevel::DEBUG));
    }

    public function testCallMagicMethodThatDoesNotExist(): void
    {
        $logger = new TestLogger();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Call to undefined method');

        $logger->someMethodThatDoesNotExist(); // @phpstan-ignore-line
    }

    /**
     * @dataProvider provideInvalidLogLevels
     */
    public function testLogWithUnsupportedLevel(mixed $invalidLogLevel): void
    {
        $logger = new TestLogger();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported log level');

        $logger->log($invalidLogLevel, 'Test');
    }

    /**
     * @return iterable<string, array{0: mixed}>
     */
    public function provideInvalidLogLevels(): iterable
    {
        yield 'null' => [null];
        yield 'bool' => [true];
        yield 'float' => [1.0];
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
        yield 'resource' => [\fopen('php://memory', 'r')];
        yield 'callable' => [static fn () => null];
    }

    public function testCustomLogLevels(): void
    {
        $recordsToLog = [
            0 => 'Emergency',
            1 => 'Alert',
            2 => 'Critical',
            3 => 'Error',
            4 => 'Warning',
            5 => 'Notice',
            6 => 'Informational',
            7 => 'Debug',
            'super low priority' => 'Super Low Priority',
        ];

        $logger = new TestLogger();

        foreach ($recordsToLog as $level => $message) {
            $logger->log($level, $message);
            $this->assertTrue($logger->hasRecord($message, $level));
        }

        $this->assertCount(\count($recordsToLog), $logger->records);

        // Custom log levels don't work with the magic methods
        $this->assertFalse($logger->hasEmergencyRecords());
        $this->assertFalse($logger->hasAlertRecords());
        $this->assertFalse($logger->hasCriticalRecords());
        $this->assertFalse($logger->hasErrorRecords());
        $this->assertFalse($logger->hasWarningRecords());
        $this->assertFalse($logger->hasNoticeRecords());
        $this->assertFalse($logger->hasInfoRecords());
        $this->assertFalse($logger->hasDebugRecords());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function provideLogLevels(): iterable
    {
        yield [LogLevel::DEBUG];
        yield [LogLevel::INFO];
        yield [LogLevel::NOTICE];
        yield [LogLevel::WARNING];
        yield [LogLevel::ERROR];
        yield [LogLevel::CRITICAL];
        yield [LogLevel::ALERT];
        yield [LogLevel::EMERGENCY];
    }
}
