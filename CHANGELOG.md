# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog and this project follows Semantic Versioning where practical.

## [Unreleased]

### Fixed

- Resolved a fatal inheritance conflict in `XoopsCollection`.
- Hardened `Optional` so non-object method calls return `null` instead of throwing.
- Corrected `Arr::isAssoc([])` to return `false`.
- Added integration coverage for XOOPS collection helpers.

### Added

- Added PHPStan configuration and XOOPS stubs for static analysis.
- Added repository health files, issue forms, PR template, and dependency/security workflows.
