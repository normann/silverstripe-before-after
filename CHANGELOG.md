# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 2026-10-27

### Fixed

- Unpublishing or archiving a `BeforeAfterImageBlock` no longer unpublishes or archives the
  `BeforeAfterImage` it links to. A `BeforeAfterImage` can be shared by several blocks, so
  `BeforeAfterImageBlock` no longer declares `$cascade_deletes`. The image now stays in the
  "Before ‹[]› After" admin section until you delete it there.
- The "Used on" tab of a file used by a `BeforeAfterImage` now links to the right edit form.
  `BeforeAfterImage` now has `CMSEditLinkExtension` applied, with `BeforeAfterImageAdmin` as its
  `cms_edit_owner`.

## [1.0.0] - 2026-09-17

First release, for SilverStripe 5 (`php` ^8.1, `silverstripe/framework` ^5.0).

### Added

- `BeforeAfterImage` DataObject widget with a dedicated `ModelAdmin` section.
  - Configurable slider direction (horizontal/vertical/diagonal), label visibility and
    positioning, and an optional "new look" badge (shape, colours, text, offset).
  - Both `BadgeShape` and `SliderDirection` show an indicative icon per option (a two-tone
    rectangle with the actual drag-handle glyph, for direction) rather than a plain text label.
  - `BeforeImage`/`AfterImage` are published automatically on save, and `$owns`,
    `$cascade_deletes` and `$cascade_duplicates` are all declared so publishing, unpublishing,
    archiving and duplicating a `BeforeAfterImage` (directly, or as part of an owning
    `BeforeAfterImageBlock`/page) carry through to its images consistently.
  - Custom `GridFieldDetailForm` so the widget's edit form gets its own CSS class.
- `BeforeAfterImageBlock` Elemental content block wrapping the widget, with optional
  content/title and a choice of four layouts.
- Dependency-free front-end JavaScript (drag + touch support, lazy init via
  `IntersectionObserver`, resize handling) and a single stylesheet, with no dependency on
  Bootstrap or any other CSS framework — the templates carry only the module's own classes.
- CMS admin styling for the section icon and the widget's edit form.

[Unreleased]: https://github.com/normann/silverstripe-before-after/compare/1.0.1...1
[1.0.1]: https://github.com/normann/silverstripe-before-after/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/normann/silverstripe-before-after/releases/tag/1.0.0
