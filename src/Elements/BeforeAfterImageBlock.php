<?php

namespace Normann\BeforeAfter\Elements;

use DNADesign\Elemental\Models\BaseElement;
use Normann\BeforeAfter\Model\BeforeAfterImage;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Requirements;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class BeforeAfterImageBlock extends BaseElement
{
    private static string $icon = 'icon-before-after-image-block';

    private static string $table_name = 'ElementBeforeAfterImage';

    private static string $singular_name = 'before/after image block';

    private static string $plural_name = 'before/after image blocks';

    private static bool $inline_editable = false;

    private static string $description =
        'Block to compare "before" and "after" looks of an image';

    private static array $db = [
        'ShowContent' => 'Boolean',
        'Content' => 'HTMLText',
        'Layout' => 'Enum("contentTop, contentRight, contentBottom, contentLeft", "contentTop")',
        'AlignThemeWithBeforeAfterImageBadge' => 'Boolean(0)',
    ];

    private static array $has_one = [
        'BeforeAfterImage' => BeforeAfterImage::class,
    ];

    private static array $owns = [
        'BeforeAfterImage',
    ];

    private static array $cascade_deletes = [
        'BeforeAfterImage',
    ];

    private static array $cascade_duplicates = [
        'BeforeAfterImage',
    ];

    private static array $field_labels = [
        'AlignThemeWithBeforeAfterImageBadge' => 'Align the block\'s theme with the Badge\'s color setup of the '
            . 'associated Before/After Image?'
    ];

    public const LAYOUT_MAPPING = [
        'contentTop' => 'Content is on top of the Before/After slider/image',
        'contentRight' => 'Before/After slider/image is on left, holding 2/3 container\'s width, '
            . 'content is on right holding 1/3 container\'s width, wrapping around slider/image when it needs to',
        'contentBottom' => 'Before/After slider/image is on top of the Content',
        'contentLeft' => 'Before/After slider/image is on right, holding 2/3 container\'s width, '
            . 'content is on left holding 1/3 container\'s width, wrapping around slider/image when it needs to',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $showContentDescription = <<<HTML
<p class="message good mt-1">
   Hide the title and content above to use this block as a plain image slider.
</p>
HTML;
        $fields->dataFieldByName('ShowContent')?->setDescription(
            $showContentDescription
        );

        $themeAlignmentDescription = <<<HTML
<p class="message good mt-1">
   Matches this block's colours to the linked image's badge, even if that badge is hidden.
</p>
HTML;
        $fields->dataFieldByName('AlignThemeWithBeforeAfterImageBadge')?->setDescription(
            $themeAlignmentDescription
        );

        $layoutField = $fields->dataFieldByName('Layout')->setSource(self::LAYOUT_MAPPING);

        $contentFields = Wrapper::create(
            [$fields->dataFieldByName('Content'), $layoutField]
        );

        $fields->removeByName(['Content', 'Layout']);

        $fields->addFieldToTab('Root.Main', $contentFields, 'BeforeAfterImageID');

        $contentFields->displayIf('ShowContent')->isChecked();

        return $fields;
    }

    public function getSummary()
    {
        return DBField::create_field('HTMLText', 'Before/After Image slider: ' . $this->Content)
            ->Summary(20);
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Before/After Image Slider');
    }

    public function forTemplate($holder = true): string
    {
        Requirements::css(
            ModuleResourceLoader::resourcePath('normann/silverstripe-before-after: client/dist/css/before-after.css')
        );

        return parent::forTemplate($holder);
    }

    /**
     * @return string
     */
    public function getBoxShadowStyle(): string
    {
        $colorScheme = $this->getColorScheme();
        return sprintf('box-shadow: 0 2px 8px #%s44;', $colorScheme['bgColor']);
    }

    public function getFrontgroundColor(): string
    {
        $colorScheme = $this->getColorScheme();

        return '#' . $colorScheme['fgColor'];
    }

    /**
     * @return array|string[]
     */
    public function getColorScheme(): array
    {
        $widget = $this->BeforeAfterImage();

        $defaultBgColor = '000000';
        $defaultFgColor = 'var(--typography-color)';

        if ($this->AlignThemeWithBeforeAfterImageBadge && $widget->exists()) {
            return [
                'bgColor' => $widget->BadgeBackgroundColor ?? $defaultBgColor,
                'fgColor' => $widget->BadgeTextColor ?? $defaultFgColor,
            ];
        }

        return [
            'bgColor' => $defaultBgColor,
            'fgColor' => $defaultFgColor,
        ];
    }
}
