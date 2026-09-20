<?php

namespace Normann\BeforeAfter\Forms;

use BimTheBam\NativeColorInput\Form\Field\ColorField as NativeColorField;

/**
 * The DB columns this feeds (BadgeBackgroundColor/BadgeTextColor) store a bare 6-hex-digit
 * string with no leading '#' — callers elsewhere (templates, BeforeAfterImageBlock) prepend
 * '#' themselves. A native <input type="color"> requires '#rrggbb' to display and submits
 * '#rrggbb' back, so this strips/adds the '#' at the boundary and keeps storage unchanged.
 *
 * FormField has no Value() method: the rendered <input>'s value attribute comes from
 * getDefaultAttributes() calling getFormattedValue() -> getValue(), while dataValue() (used by
 * saveInto()) reads $this->value directly. Normalizing in setValue()/getValue() keeps $this->value
 * — and therefore dataValue() — a bare hex string, while the rendered attribute always has '#'.
 */
class ColorField extends NativeColorField
{
    public function setValue($value, $data = null)
    {
        if (is_string($value)) {
            $value = ltrim($value, '#');
        }

        return parent::setValue($value, $data);
    }

    public function getValue(): mixed
    {
        $value = parent::getValue();

        return '#' . ltrim((string) ($value ?: '000000'), '#');
    }
}
