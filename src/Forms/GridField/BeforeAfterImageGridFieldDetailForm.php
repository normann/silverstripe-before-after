<?php

namespace Normann\BeforeAfter\Forms\GridField;

use SilverStripe\Forms\GridField\GridFieldDetailForm;

class BeforeAfterImageGridFieldDetailForm extends GridFieldDetailForm
{
    protected $itemRequestClass = BeforeAfterImageGridFieldDetailForm_ItemRequest::class;
}
