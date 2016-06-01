# CHANGELOG

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- `LitGroup\Sms\Exception\InvalidArgumentException`

### Changed
- This library is independent from providers now. All provider specific
  implementations of `GatewayInterface` were removed.
- This library does not depend on `guzzlehttp/guzzle` anymore.
- Copyright was transited to `© Roman Shamritskiy <roman@litgroup.ru>`.
- `Message` is an immutable value-object now. Setters removed.
- Arguments `$body` and `$recipients` of `Message::__construct()` are required now.
- Message will not try to normalize phone number of recipient anymore.
  Number must be presented in the form `+71231234567` or `InvalidArgumentException`
  will be thrown.
- `LitGroup\Sms\Exception\GatewayException` now extends `\Exception` instead of `\RuntimeException`.
- `SmsExceptionInterface` renamed to `ExceptionInterface`.

### Removed
- Removed class `LitGroup\Sms\Gateway\MockSms\MockSmsGateway`
- Removed class `LitGroup\Sms\Gateway\Smsc\SmscGateway`
- Method `Message::setBody()` was removed. Use constructor's argument instead.
- Method `Message::setRecipients()` was removed. Use constructor's argument instead.
- Method `Message::addRecipirnt()` was removed. Use constructor's argument instead.
- Method `Message::setSender()` was removed. Use constructor's argument instead.
- Method `MessageServiceInterface::createMessage()` was removed from interface.
- Method `MessageService::createMessage()` was removed.
- Method `MessageServiceInterface::setMessageLogger()` was removed.
- Method `MessageService::setMessageLogger()` was removed.
- `MessageLoggerInterface`, `MessageLogger`, `NullMessageLogger` were removed.


## [0.5.0] - 2015-12-16
### Added
- MockSmsGateway` (EXPERIMENTAL, see: https://github.com/LitGroup/mock-sms-server).


## [0.4.0] - 2015-12-15
### Added
- Added `MessageLoggerInterface`.
- [BC] `MessageServiceInterface::setMessageLogger` was added.

## [0.3.0] - 2015-12-15
### Added
- Added `NullGateway` for development purposes.
- Added `CascadeGateway`.


## [0.2.1] - 2015-12-14
### Changed
- `MessageService` logger messages changed.


## [0.2.0] - 2015-12-14
### Added
- Added clarifying exceptions.
- `MessageService` implements `PSR-3` `LoggerAwareInterface`.


## [0.1.0] - 2015-12-11
- Initial version.