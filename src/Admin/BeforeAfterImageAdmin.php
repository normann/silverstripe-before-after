<?php

namespace Normann\BeforeAfter\Admin;

use Normann\BeforeAfter\Forms\GridField\BeforeAfterImageGridFieldDetailForm;
use Normann\BeforeAfter\Model\BeforeAfterImage;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDetailForm;

class BeforeAfterImageAdmin extends ModelAdmin
{
    private static string $menu_title = 'Before ‹[]› After';

    private static string $menu_icon = 'normann/silverstripe-before-after: client/dist/images/icon.svg';

    private static array $managed_models = [
        BeforeAfterImage::class,
    ];

    private static string $url_segment = 'before-after';

    /**
     * @param $id
     * @param $fields
     * @return Form
     */
    public function getEditForm($id = null, $fields = null): Form
    {
        $form = parent::getEditForm($id, $fields);

        $gridField = $form->Fields()->fieldByName($this->sanitiseClassName(BeforeAfterImage::class));

        if ($gridField instanceof GridField) {
            $config = $gridField->getConfig();

            // Replace the existing GridFieldDetailForm with our custom one
            $config->removeComponentsByType(GridFieldDetailForm::class);
            $config->addComponent(new BeforeAfterImageGridFieldDetailForm());
        }

        return $form;
    }
}
