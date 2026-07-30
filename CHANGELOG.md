# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.1] - 2026-07-30

### Fixed

- The carNumber block attribute was not being updated on post save when the Tucker block was nested inside another block (e.g. a Kadence row). The block search now walks the full block tree recursively so the attribute is found and updated regardless of nesting depth.
- Replaced the anonymous closure used for the recursive block walk with a named top-level function (`tucker_set_car_number_in_blocks`) and removed all by-reference variable passing.

## [0.2.0] - 2026-07-30

### Added

- Rich text support for the car notes field, including formatted links and basic formatting.
- Front-end rendering for the saved notes content so rich HTML appears correctly on the site.

### Changed

- Updated the block save/serialization flow so notes and block attributes persist correctly.
- Added automatic syncing from a car post title in the form "Tucker nnnn" to the block's carNumber value, the gallery shortcode category, and the post slug.
- Added inline documentation to help future PHP contributors understand the sync logic.

## [0.1.1] - 2026-07-10

### Added 

- Initial release. 
- Add CHANGELOG