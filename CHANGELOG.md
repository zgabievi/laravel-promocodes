# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 2017-08-29
### Fixed
- Migration file, that was not updated.

### Added
- Ability to create multi-use or one-time promotion codes.

## [1.0.0] - 2017-08-29
### Added
- Ability to set expiration date of promocode while creating.
- Ability to remove all redundant (expired or used) promocodes from database.
- Invalid Promocode Exception, Unauthenticated Exception, Already Used Exception.
- Ability to disable promocode using code string (Promocode will be expired).
- Support for Laravel 5.5 Package Auto-Discovery.

### Changed
- Migration & config file. Now promocode & user will be related through pivot table. [#12]

### Fixed
- Migration problem where database couldn't support json type. [#13]

### Removed
- Ability of user, that they could create promocodes assigned to them.

[Unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v1.0.0...HEAD
[1.0.1]: https://github.com/zgabievi/laravel-promocodes/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/zgabievi/laravel-promocodes/compare/v0.5.4...v1.0.0

[#12]: https://github.com/zgabievi/laravel-promocodes/issues/12
[#13]: https://github.com/zgabievi/laravel-promocodes/issues/13
