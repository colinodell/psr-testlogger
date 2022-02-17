<?php

declare(strict_types=1);

namespace ColinODell\PsrTestLogger\Tests\Unit;

use ColinODell\PsrTestLogger\TestLogger;
use PHPUnit\Framework\TestCase;
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
    }

    public function testReset(): void
    {
        $logger = new TestLogger();

        $logger->log(LogLevel::DEBUG, 'Test');
        $this->assertTrue($logger->hasRecords(LogLevel::DEBUG));

        $logger->reset();
        $this->assertFalse($logger->hasRecords(LogLevel::DEBUG));
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
