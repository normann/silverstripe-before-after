# Before/After Image

## Overview

This module provides a draggable "before/after" image comparison slider, as:

- a standalone `BeforeAfterImage` DataObject, managed in its own CMS section, and
- a `BeforeAfterImageBlock` Elemental content block that places one on a page.

## The `BeforeAfterImage` record

Fields are grouped into four CMS tabs:

- **Slider** — `SliderDirection` (horizontal / vertical / diagonal left / diagonal right) and
  `SliderDefaultOffset` (initial position, 0–100%).
- **Before** / **After** — the image, an optional caption, a label, and label positioning
  (corner for horizontal/diagonal sliders, left/center/right for vertical sliders), each with a
  fine offset control.
- **Badge** — an optional shape (star/circle/rectangle) with text, background/text colour and a
  position offset, useful for a "New Look!" style callout.

`LabelsVisibility` controls whether the Before/After labels are always shown, shown on hover, or
hidden entirely.

## The `BeforeAfterImageBlock` element

Once [dnadesign/silverstripe-elemental](https://github.com/silverstripe/silverstripe-elemental)
is installed, a **"Before/After Image Slider"** block becomes available wherever an
`ElementalArea` is editable. It has:

- `BeforeAfterImage` — a `has_one` to the widget above (created or selected inline).
- `ShowContent` / `Content` — optional rich text alongside the slider.
- `Layout` — `contentTop`, `contentBottom`, `contentLeft` or `contentRight`, controlling where
  the content sits relative to the slider.
- `AlignThemeWithBeforeAfterImageBadge` — when checked, the block's own colour accents (box
  shadow, text colour) follow the linked widget's badge colours.

## Extending

Both classes are plain SilverStripe `DataObject`/`BaseElement` subclasses, so the usual extension
points apply — e.g. add a `DataExtension` to add fields, or override the `.ss` templates shipped
under `templates/Normann/BeforeAfter/` in your own project's theme to change markup.

## Front-end assets

The module ships a single pre-built JavaScript file and a single stylesheet under
`client/dist/`, loaded on demand via `SilverStripe\View\Requirements` — there is no build step to
run in the consuming project. If you want to customise the slider behaviour or styling
extensively, the recommended approach is to override `Requirements` for these paths in your own
project (e.g. via `Requirements::block()` and enqueueing your own asset).
