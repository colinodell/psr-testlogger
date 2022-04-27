# psr-testlogger

[![Latest Version](https://img.shields.io/packagist/v/colinodell/psr-testlogger.svg?style=flat-square)](https://packagist.org/packages/colinodell/psr-testlogger)
[![Total Downloads](https://img.shields.io/packagist/dt/colinodell/psr-testlogger.svg?style=flat-square)](https://packagist.org/packages/colinodell/psr-testlogger)
[![Software License](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/workflow/status/colinodell/psr-testlogger/Tests/main.svg?style=flat-square)](https://github.com/colinodell/psr-testlogger/actions?query=workflow%3ATests+branch%3Amain)
[![Psalm Type Coverage](https://shepherd.dev/github/colinodell/psr-testlogger/coverage.svg)](https://shepherd.dev/github/colinodell/psr-testlogger)
[![Sponsor development of this project](https://img.shields.io/badge/sponsor%20this%20package-%E2%9D%A4-ff69b4.svg?style=flat-square)](https://www.colinodell.com/sponsor)

PSR-3 compliant test logger based on psr/log v1's

## 📦 Installation

This project requires PHP 7.4 or higher.  To install it via Composer simply run:

``` bash
$ composer require colinodell/psr-testlogger
```

## Usage

This package provides a PSR-3 compliant logger useful for testing.  Simply log messages to it like usual, and use one of the many available methods to perform assertions on the logged messages.

```
hasRecords(string $level): bool

hasEmergencyRecords(): bool
hasAlertRecords(): bool
hasCriticalRecords(): bool
hasErrorRecords(): bool
hasWarningRecords(): bool
hasNoticeRecords(): bool
hasInfoRecords(): bool
hasDebugRecords(): bool

hasRecord(string|array $record, string $level): bool

hasEmergency(string|array $record): bool
hasAlert(string|array $record): bool
hasCritical(string|array $record): bool
hasError(string|array $record): bool
hasWarning(string|array $record): bool
hasNotice(string|array $record): bool
hasInfo(string|array $record): bool
hasDebug(string|array $record): bool

hasRecordThatContains(string $message, string $level): bool

hasEmergencyThatContains(string $message): bool
hasAlertThatContains(string $message): bool
hasCriticalThatContains(string $message): bool
hasErrorThatContains(string $message): bool
hasWarningThatContains(string $message): bool
hasNoticeThatContains(string $message): bool
hasInfoThatContains(string $message): bool
hasDebugThatContains(string $message): bool

hasRecordThatMatches(string $regex, string $level): bool

hasEmergencyThatMatches(string $regex): bool
hasAlertThatMatches(string $regex): bool
hasCriticalThatMatches(string $regex): bool
hasErrorThatMatches(string $regex): bool
hasWarningThatMatches(string $regex): bool
hasNoticeThatMatches(string $regex): bool
hasInfoThatMatches(string $regex): bool
hasDebugThatMatches(string $regex): bool

hasRecordThatPasses(callable $predicate, string $level): bool
hasEmergencyThatPasses(callable $predicate): bool
hasAlertThatPasses(callable $predicate): bool
hasCriticalThatPasses(callable $predicate): bool
hasErrorThatPasses(callable $predicate): bool
hasWarningThatPasses(callable $predicate): bool
hasNoticeThatPasses(callable $predicate): bool
hasInfoThatPasses(callable $predicate): bool
hasDebugThatPasses(callable $predicate): bool
```
