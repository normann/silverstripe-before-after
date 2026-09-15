<?php

namespace Normann\BeforeAfter\Forms\GridField;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;

// Named to match the SilverStripe core convention for GridFieldDetailForm_ItemRequest subclasses.
// phpcs:ignore Squiz.Classes.ValidClassName
class BeforeAfterImageGridFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest
{
    private static array $allowed_actions = [
        'ItemEditForm',
    ];

    /**
     * Overrides the parent's ItemEditForm(); the name is fixed by SilverStripe core.
     * @return HTTPResponse|Form
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function ItemEditForm(): HTTPResponse|Form
    {
        $form = parent::ItemEditForm();

        // Add a specific class
        $form->addExtraClass('before-after-image-edit-form');

        return $form;
    }
}
