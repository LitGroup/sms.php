# CHANGELOG

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).


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

## [v0.2.0] - 2015-12-14
### Added
- Added clarifying exceptions.
- `MessageService` implements `PSR-3` `LoggerAwareInterface`.

## [0.1.0] - 2015-12-11
- Initial version.