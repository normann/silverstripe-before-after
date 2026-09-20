# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

Nothing has been tagged or published yet — this module has no releases. Everything below is
still local, pre-first-commit work in progress.

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
- SilverStripe 6 support: `php` ^8.3, `silverstripe/framework` ^6.0, `silverstripe/admin` ^3.0,
  `silverstripe/assets` ^3.0, `silverstripe/versioned` ^3.0, `dnadesign/silverstripe-elemental`
  ^6.0, `unclecheese/display-logic` ^4.0, `phpunit/phpunit` ^11.3, `silverstripe/recipe-testing`
  ^4.

### Changed

- Replaced `tractorcow/silverstripe-sliderfield` with `firesphere/rangefield` — no SS6-compatible
  release of the tractorcow module exists.
- Replaced `tractorcow/silverstripe-colorpicker` with
  `bimthebam/silverstripe-native-color-input` — no SS6-compatible release of the tractorcow
  module exists. A local `Normann\BeforeAfter\Forms\ColorField` wraps the new native
  `<input type="color">` field to keep the `#`-less 6-hex-digit storage format the
  `BadgeBackgroundColor`/`BadgeTextColor` DB columns expect.
- Updated `BeforeAfterImage::forTemplate()` and `BeforeAfterImageBlock::forTemplate()` to declare
  a `string` return type, matching SS6's `ModelData::forTemplate()`/`BaseElement::forTemplate()`
  signatures (both previously had no/incompatible return types, which is a fatal error under SS6).
- Updated the `SilverStripe\Forms\CompositeValidator` import to `SilverStripe\Forms\Validation\CompositeValidator`,
  matching the class's new location in SS6.
