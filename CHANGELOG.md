# Changelog

All notable changes to this project will be documented in this file.

## [v2.0.0] - 2026-06-26
### Added
- New validation rules:
  - Alpha
  - AlphaNum
  - Boolean
  - Integer
  - Numeric
  - Regex
  - Url
- Support for writing custom rules by extending `BaseRule`
- A `RuleException` is now thrown when a custom rule matches the name of a built-in rule
- A `PreparesValue` contract and `PreparesDate` trait, allowing a rule to transform a value before it is validated (used by the built-in date rules to cast date strings to `DateTimeInterface`)

### Changed
- Renamed the abstract `Rule` rule class to `BaseRule`; the validation contract now lives at `Contracts\Rule`. If you extended the old `Rules\Rule` class directly, update it to extend `Rules\BaseRule` (note that the previous `getMessage()` method has also been removed).
- Replaced the `ValidatesDates` and `ValidatesDatesOrNormalStrings` contracts with the single `PreparesValue` contract

### Fixed
- Uninitialised typed properties are now validated as `null` instead of being skipped, so rules such as `Required` correctly fail for them

## [v1.0.1] - 2025-06-26
### Fixed
- Fixed incorrect `passes()` method call in main validation rule handler

## [v1.0.0] - 2025-04-19
### Added
#### Initial release! 🚀  
- Core functionality implemented
- Project structure established
- Added validation rules:
  - Required
  - Email
  - In
  - Equal
  - Between
  - GreaterThan
  - GreaterOrEqual
  - LessThan
  - LessOrEqual
  - MinLength
  - MaxLength
- Added custom validation message support
- Added validation error reporting and handling

---

## Format  
This changelog follows the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) format.  
