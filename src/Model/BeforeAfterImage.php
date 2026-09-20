<?php

namespace Normann\BeforeAfter\Model;

use Firesphere\RangeField\RangeField;
use Normann\BeforeAfter\Elements\BeforeAfterImageBlock;
use Normann\BeforeAfter\Forms\ColorField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\Validation\CompositeValidator;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class BeforeAfterImage extends DataObject
{
    private static string $table_name = 'BeforeAfterImage';

    private static string $singular_name = 'before/after image';

    private static string $plural_name = 'before/after images';

    private static string $description =
        'An image with its "before" and "after" looks being comparable by dragging a slider around';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'HideTitle' => 'Boolean(1)',
        'BeforeImageCaption' => 'Varchar(255)',
        'AfterImageCaption' => 'Varchar(255)',
        'SliderDirection' => 'Enum("horizontal, vertical, diagonalLeft, diagonalRight")',
        'SliderDefaultOffset' => 'Int', // a percentage from 0 to 100
        'LabelsVisibility' => 'Enum("alwaysShow, onMouseOver, hideLabels")',
        'BeforeLabel' => 'Varchar(255)',
        'AfterLabel' => 'Varchar(255)',
        'BeforeLabelPosition' => 'Enum("topLeft, middleLeft, bottomLeft", "bottomLeft")',
        'AfterLabelPosition' => 'Enum("topRight, middleRight, bottomRight", "bottomRight")',
        'BeforeLabelPositionVertical' => 'Enum("left, center, right", "left")',
        'AfterLabelPositionVertical' => 'Enum("left, center, right", "left")',
        'BeforeLabelOffsetVertical' => 'Int', // a percentage from 0 to 100
        'BeforeLabelOffsetHorizontal' => 'Int', // a percentage from 0 to 100
        'AfterLabelOffsetVertical' => 'Int', // a percentage from 0 to 100
        'AfterLabelOffsetHorizontal' => 'Int', // a percentage from 0 to 100
        'ShowBadge' => 'Boolean(1)',
        'BadgeShape' => 'Enum("star, circle, rectangle")',
        'BadgeText' => 'Varchar(255)',
        'BadgeBackgroundColor' => 'Varchar(6)',
        'BadgeTextColor' => 'Varchar(6)',
        'BadgeOffsetTop' => 'Int', // 0 - 100 as a percentage
        'BadgeOffsetLeft' => 'Int', // 0 - 100 as a percentage
    ];

    private static array $has_one = [
        'BeforeImage' => Image::class,
        'AfterImage' => Image::class,
    ];

    private static array $has_many = [
        'BlockBeingSelected' => BeforeAfterImageBlock::class,
    ];

    private static array $owns = [
        'BeforeImage',
        'AfterImage',
    ];

    private static array $cascade_deletes = [
        'BeforeImage',
        'AfterImage',
    ];

    private static array $cascade_duplicates = [
        'BeforeImage',
        'AfterImage',
    ];

    private static array $summary_fields = [
        'Title' => 'Title',
        'SliderDirection' => 'Slider moving direction',
        'LabelsVisibility' => 'Labels visibility',
        'BeforeImage.StripThumbnail' => 'Before image',
        'AfterImage.StripThumbnail' => 'After image',
        'BeforeLabel' => 'Before label',
        'AfterLabel' => 'After label',
        'ShowBadge' => 'Badge display?',
        'BadgeText' => 'Text on badge',
    ];

    private static array $defaults = [
        'HideTitle' => true,
        'SliderDefaultOffset' => 50,
        'BeforeImageCaption'  => 'Before Image Caption',
        'AfterImageCaption'  => 'After Image Caption',
        'BeforeLabel' => 'Before',
        'AfterLabel' => 'After',
        'BeforeLabelPosition' => 'bottomLeft',
        'AfterLabelPosition' => 'bottomRight',
        'BeforeLabelPositionVertical' => 'left',
        'AfterLabelPositionVertical' => 'left',
        'BeforeLabelOffsetVertical' => 3,
        'AfterLabelOffsetVertical' => 3,
        'BeforeLabelOffsetHorizontal' => 2,
        'AfterLabelOffsetHorizontal' => 2,
        'ShowBadge' => true,
        'BadgeText' => 'A New Look!',
        'BadgeBackgroundColor' => '0F5FF8',
        'BadgeTextColor' => 'FFFFFF',
        'BadgeOffsetTop' => 3,
        'BadgeOffsetLeft' => 3,
    ];

    private static array $field_labels = [
        'BeforeImage' => 'Image',
        'AfterImage' => 'Image',
        'BeforeImageCaption' => 'Image caption',
        'AfterImageCaption' => 'Image caption',
        'BeforeLabel' => 'Label',
        'AfterLabel' => 'Label',
        'BeforeLabelPosition' => 'Label position',
        'AfterLabelPosition' => 'Label position',
        'BeforeLabelPositionVertical' => 'Label position',
        'AfterLabelPositionVertical' => 'Label position',
        'BeforeLabelOffsetVertical' => 'Label offset vertical',
        'AfterLabelOffsetVertical' => 'Label offset vertical',
        'BeforeLabelOffsetHorizontal' => 'Label offset horizontal',
        'AfterLabelOffsetHorizontal' => 'Label offset horizontal',
    ];

    public const LABELS_VISIBILITY = [
        'alwaysShow' => 'Always show',
        'onMouseOver' => 'On mouseover',
        'hideLabels' => 'Hide labels',
    ];

    public const SLIDER_DIRECTION = [
        'horizontal' => 'Horizontal',
        'vertical' => 'Vertical',
        'diagonalLeft' => 'Right-Top to Left-Bottom',
        'diagonalRight' => 'Left-Top to Right-Bottom',
    ];

    public const BEFORE_LABEL_POSITION = [
        'topLeft' => 'Top left',
        'middleLeft' => 'Middle left',
        'bottomLeft' => 'Bottom left',
    ];

    public const AFTER_LABEL_POSITION = [
        'topRight' => 'Top right',
        'middleRight' => 'Middle right',
        'bottomRight' => 'Bottom right',
    ];

    public const LABEL_POSITION_VERTICAL = [
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
    ];

    public const BADGE_SHAPE = [
        'star' => 'Star',
        'circle' => 'Circle',
        'rectangle' => 'Rectangle',
    ];

    /**
     * Maps each SliderDirection enum value to the filename (without extension) of its
     * indicative icon under client/dist/images/.
     */
    public const SLIDER_DIRECTION_ICON = [
        'horizontal' => 'direction-horizontal',
        'vertical' => 'direction-vertical',
        'diagonalLeft' => 'direction-diagonal-left',
        'diagonalRight' => 'direction-diagonal-right',
    ];

    public const NUMERIC_FIELDS = [
        'SliderDefaultOffset',
        'BeforeLabelOffsetVertical',
        'AfterLabelOffsetVertical',
        'BeforeLabelOffsetHorizontal',
        'AfterLabelOffsetHorizontal',
        'BadgeOffsetTop',
        'BadgeOffsetLeft',
    ];

    public const COLOR_FIELDS = [
        'BadgeBackgroundColor',
        'BadgeTextColor',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            // Do basic grooming first
            $this->basicGrooming($fields);

            // Split fields into several tabs "Slider", "Before", "After", and "Badge"
            $badgeWrapper = Wrapper::create();
            $this->splitIntoTabs($fields, $badgeWrapper);
            $badgeWrapper->displayIf('ShowBadge')->isChecked();

            // Turn numeric/int fields into sliderFields
            $this->turnNumericToSlider($fields);

            // Apply Color picker for each of the color fields
            $this->applyColorPicker($fields);

            // Apply Imagery label of BadgeShape
            $this->applyImageryLabel($fields);

            // Apply Imagery label of SliderDirection
            $this->applyDirectionImagery($fields);

            // Fine-tuning the display fields
            $this->toggleLabelFieldsVisibility($fields);
        });

        return parent::getCMSFields();
    }

    public function basicGrooming(FieldList &$fields): void
    {
        $fields
            ->dataFieldByName('Title')
            ->setDescription('<div class="alert alert-info">'
                . 'Only visible in the CMS if you check "Hide title" below — it won\'t show on the page.'
                . '</div>');

        $fields->replaceField(
            'LabelsVisibility',
            OptionsetField::create(
                'LabelsVisibility',
                'Labels visibility',
                self::LABELS_VISIBILITY,
                'alwaysShow'
            )
        );

        $fields->replaceField(
            'SliderDirection',
            OptionsetField::create(
                'SliderDirection',
                'Slider moving direction',
                self::SLIDER_DIRECTION,
                'horizontal'
            )
        );

        $fields->replaceField(
            'BadgeShape',
            OptionsetField::create(
                'BadgeShape',
                'Badge shape',
                self::BADGE_SHAPE,
                'star'
            )
        );

        $fields->replaceField(
            'BeforeLabelPosition',
            $beforeLabelPosition = DropdownField::create(
                'BeforeLabelPosition',
                'Label position',
                self::BEFORE_LABEL_POSITION,
                'bottomLeft'
            )
        );

        $fields->replaceField(
            'AfterLabelPosition',
            $afterLabelPosition = DropdownField::create(
                'AfterLabelPosition',
                'Label position',
                self::AFTER_LABEL_POSITION,
                'bottomRight'
            )
        );

        $beforeLabelPosition->hideIf('SliderDirection')->isEqualTo('vertical');
        $afterLabelPosition->hideIf('SliderDirection')->isEqualTo('vertical');

        $fields->replaceField(
            'BeforeLabelPositionVertical',
            $beforeLabelPositionVertical = DropdownField::create(
                'BeforeLabelPositionVertical',
                'Label position',
                self::LABEL_POSITION_VERTICAL,
                'left'
            )
        );

        $fields->replaceField(
            'AfterLabelPositionVertical',
            $afterLabelPositionVertical = DropdownField::create(
                'AfterLabelPositionVertical',
                'Label position',
                self::LABEL_POSITION_VERTICAL,
                'left'
            )
        );

        $beforeLabelPositionVertical->displayIf('SliderDirection')->isEqualTo('vertical');
        $afterLabelPositionVertical->displayIf('SliderDirection')->isEqualTo('vertical');
    }

    public function splitIntoTabs(FieldList &$fields, Wrapper &$badgeWrapper): void
    {
        $fields->addFieldsToTab('Root.Main', [
            TabSet::create('ConfigSet')
        ]);

        $fields->findOrMakeTab('Root.Main.ConfigSet.Slider');
        $fields->addFieldsToTab('Root.Main.ConfigSet.Slider', [
            $fields->dataFieldByName('SliderDirection'),
            $fields->dataFieldByName('SliderDefaultOffset'),
        ]);

        $fieldsCanSplit = [
            'Image',
            'ImageCaption',
        ];

        foreach (['Before', 'After'] as $prefix) {
            $fields->findOrMakeTab('Root.Main.ConfigSet.' . $prefix, $prefix);
            $fields->addFieldsToTab(
                'Root.Main.ConfigSet.' . $prefix,
                array_map(
                    function ($field) use ($prefix, $fields) {
                        return $fields->dataFieldByName($prefix . $field);
                    },
                    $fieldsCanSplit
                )
            );
        }

        $badgeFields = [
            'BadgeShape',
            'BadgeText',
            'BadgeBackgroundColor',
            'BadgeTextColor',
            'BadgeOffsetTop',
            'BadgeOffsetLeft',
        ];

        foreach ($badgeFields as $field) {
            $badgeWrapper->push($fields->dataFieldByName($field));
        }

        $fields->removeByName($badgeFields);

        $fields->findOrMakeTab('Root.Main.ConfigSet.Badge');
        $fields->addFieldsToTab(
            'Root.Main.ConfigSet.Badge',
            [
                $fields->dataFieldByName('ShowBadge'),
                $badgeWrapper
            ]
        );
    }

    public function turnNumericToSlider(FieldList &$fields): void
    {
        foreach (self::NUMERIC_FIELDS as $field) {
            $fieldTitle = $fields->dataFieldByName($field)?->Title();
            $fields->replaceField(
                $field,
                RangeField::create($field, $fieldTitle)
                    ->setMin(0)
                    ->setMax(100)
                    ->setDecimalPlaces(0)
            );
        }

        $verticalOffsetDescription = <<<HTML
<div class="alert alert-info">Sets the gap between the label and the top or bottom edge of the image, as a
percentage — which edge depends on the label position you chose above. Keep this small for a vertical slider,
or a large value may get hidden behind the slider handle.</div>
HTML;
        $fields->dataFieldByName('BeforeLabelOffsetVertical')->setDescription($verticalOffsetDescription);
        $fields->dataFieldByName('AfterLabelOffsetVertical')->setDescription($verticalOffsetDescription);

        $horizontalOffsetDescription = <<<HTML
<div class="alert alert-info">Sets the gap between the label and the left or right edge of the image, as a
percentage — which edge depends on the label position you chose above. Keep this small for a horizontal slider,
or a large value may get hidden behind the slider handle.</div>
HTML;
        $fields->dataFieldByName('BeforeLabelOffsetHorizontal')->setDescription($horizontalOffsetDescription);
        $fields->dataFieldByName('AfterLabelOffsetHorizontal')->setDescription($horizontalOffsetDescription);
    }

    public function applyColorPicker(FieldList &$fields): void
    {
        foreach (self::COLOR_FIELDS as $field) {
            $fieldTitle = $fields->dataFieldByName($field)?->Title();
            $fields->replaceField($field, ColorField::create($field, $fieldTitle));
        }
    }

    public function applyImageryLabel(FieldList &$fields): void
    {
        $resourceURL = ModuleResourceLoader::resourceURL('normann/silverstripe-before-after: client/dist/images');

        $badgeShapeSource = array_map(function ($shape) use ($resourceURL) {
            $label = self::BADGE_SHAPE[$shape];

            $htmlImageFormat = '<img class="badge-shape-image"'
                . ' src="%s/%s.svg"'
                . ' title="shape: %s" alt="%s">';

            $imageHTML = sprintf($htmlImageFormat, $resourceURL, $shape, $label, $label);

            return DBHTMLText::create('Title')->setValue($imageHTML);
        }, array_combine(array_keys(self::BADGE_SHAPE), array_keys(self::BADGE_SHAPE)));

        $fields->dataFieldByName('BadgeShape')->setSource($badgeShapeSource);
    }

    public function applyDirectionImagery(FieldList &$fields): void
    {
        $resourceURL = ModuleResourceLoader::resourceURL('normann/silverstripe-before-after: client/dist/images');

        $directionSource = array_map(function ($direction) use ($resourceURL) {
            $label = self::SLIDER_DIRECTION[$direction];
            $icon = self::SLIDER_DIRECTION_ICON[$direction];

            $htmlImageFormat = '<img class="direction-shape-image"'
                . ' src="%s/%s.svg"'
                . ' title="%s" alt="%s">';

            $imageHTML = sprintf($htmlImageFormat, $resourceURL, $icon, $label, $label);

            return DBHTMLText::create('Title')->setValue($imageHTML);
        }, array_combine(array_keys(self::SLIDER_DIRECTION), array_keys(self::SLIDER_DIRECTION)));

        $fields->dataFieldByName('SliderDirection')->setSource($directionSource);
    }

    public function toggleLabelFieldsVisibility(&$fields): void
    {
        $label_related = [
            'Label',
            'LabelPosition',
            'LabelPositionVertical',
            'LabelOffsetHorizontal',
            'LabelOffsetVertical',
        ];

        foreach (['Before', 'After'] as $prefix) {
            $label_related_wrapper = Wrapper::create();

            foreach ($label_related as $field) {
                $fieldName = $prefix . $field;
                $label_related_wrapper->push($fields->dataFieldByName($fieldName));
                $fields->removeByName($fieldName);
            }

            $fields->addFieldToTab('Root.Main.ConfigSet.' . $prefix, $label_related_wrapper);

            $label_related_wrapper->hideIf('LabelsVisibility')->isEqualTo('hideLabels');

            $labelOffsetVerticalFieldName = $prefix . 'LabelOffsetVertical';
            $labelOffsetHorizontalFieldName = $prefix . 'LabelOffsetHorizontal';

            $labelPositionFieldName = $prefix . 'LabelPosition';
            $labelPositionVerticalFieldName = $prefix . 'LabelPositionVertical';
            $labelPositionValueForOffsetVerticalHidden = $prefix === 'Before' ? 'middleLeft' : 'middleRight';


            // Hide Before-/After-LabelOffsetVertical when SliderDirection is set to 'horizontal
            // and LabelPosition is set to 'middle'
            $fields->dataFieldByName($labelOffsetVerticalFieldName)
                ?->hideIf('SliderDirection')->isEqualTo('horizontal')
                ->andIf($labelPositionFieldName)->isEqualTo($labelPositionValueForOffsetVerticalHidden);

            // Hide Before-/After-LabelOffsetHorizontal when SliderDirection is set to 'vertical'
            // and  LabelPositionVertical set to "center"
            $fields->dataFieldByName($labelOffsetHorizontalFieldName)
                ?->hideIf('SliderDirection')->isEqualTo('vertical')
                ->andIf($labelPositionVerticalFieldName)
                ->isEqualTo('center');
        }
    }

    /**
     * @return CompositeValidator
     */
    public function getCMSCompositeValidator(): CompositeValidator
    {
        $compositeValidator = parent::getCMSCompositeValidator();

        $compositeValidator->addValidator(RequiredFieldsValidator::create([
            'Title',
            'BeforeImage',
            'AfterImage'
        ]));

        return $compositeValidator;
    }

    /**
     * BeforeAfterImage itself isn't Versioned, so saving it via the CMS (e.g. the standalone
     * "Before <[]> After" admin section) never goes through a publish action -- $owns only
     * cascades when something actually gets published. Publish the linked images directly so
     * they don't get stuck in Draft after a plain save.
     */
    protected function onAfterWrite(): void
    {
        parent::onAfterWrite();

        foreach (['BeforeImage', 'AfterImage'] as $relation) {
            $image = $this->{$relation}();

            if (
                $image
                && $image->exists()
                && $image->hasExtension(Versioned::class)
                && !$image->isPublished()
            ) {
                $image->publishSingle();
            }
        }
    }

    public function forTemplate(): string
    {
        if (!$this->exists()) {
            return '';
        }

        $moduleResourcePrefix = 'normann/silverstripe-before-after: client/dist';

        Requirements::css(
            ModuleResourceLoader::resourcePath($moduleResourcePrefix . '/css/before-after.css')
        );
        Requirements::javascript(
            ModuleResourceLoader::resourcePath($moduleResourcePrefix . '/js/before-after.js')
        );

        return $this->renderWith(static::class) ?? '';
    }

    /**
     * @return false|string
     */
    public function getImageCaptionsJson(): false|string
    {
        return json_encode(
            [
                'before' => $this->BeforeImageCaption ?? "Before Image Caption",
                'after' => $this->AfterImageCaption ?? "After Image Caption",
            ]
        );
    }

    /**
     * @return false|string
     */
    public function getImageRatio(): false|string
    {
        $afterImage = $this->AfterImage();
        return json_encode(
            [
                'width' => $afterImage?->getWidth(),
                'height' => $afterImage?->getHeight(),
            ]
        );
    }

    /**
     * @return float
     */
    public function getRotationDegrees(): float
    {
        $width = $this->AfterImage()?->getWidth() ?? 0;
        $height = $this->AfterImage()?->getHeight() ?? 0;

        if ($height !== 0) {
            $rotateRadians = atan($width / $height);
            $rotateDegrees = $rotateRadians * (180 / M_PI);

            return round($rotateDegrees, 2);
        }

        return 56.12;
    }

    /**
     * @return string
     */
    public function getDiagonalHandlerStyle(): string
    {
        $degrees = $this->getRotationDegrees();

        if ($this->SliderDirection === 'diagonalLeft') {
            return sprintf('transform: translate(-50%%, -50%%) rotate(-%sdeg)', $degrees);
        } elseif ($this->SliderDirection === 'diagonalRight') {
            return sprintf('transform: translate(-50%%, -50%%) rotate(%sdeg)', $degrees);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getBadgeWrapperStyleVariables(): string
    {
        $startTop = -7.25;
        $endTop = match ($this->BadgeShape) {
            'star' => 84.75,
            'circle' => 79.75,
            'rectangle' => 80.75,
        };
        $top = $startTop + (($endTop - $startTop) * (int) $this->BadgeOffsetTop / 100);

        $startLeft = -6.75;
        $endLeft = match ($this->BadgeShape) {
            'star' => 92.25,
            'circle' => 87.25,
            'rectangle' => 85.25,
        };
        $left = $startLeft + (($endLeft - $startLeft) * (int) $this->BadgeOffsetLeft / 100);

        return sprintf('--badge-top: %d%%;--badge-left: %d%%;', $top, $left);
    }

    /**
     * @return string
     */
    public function getBeforeLabelStyle(): string
    {
        if ($this->SliderDirection === 'vertical') {
            return match ($this->BeforeLabelPositionVertical) {
                'left' => sprintf(
                    'left: %d%%; top: %d%%',
                    $this->BeforeLabelOffsetHorizontal,
                    $this->BeforeLabelOffsetVertical
                ),
                'center' => sprintf(
                    'left: 50%%; transform: translateX(-50%%); top: %d%%',
                    $this->BeforeLabelOffsetVertical
                ),
                'right' => sprintf(
                    'right: %d%%; top: %d%%',
                    $this->BeforeLabelOffsetHorizontal,
                    $this->BeforeLabelOffsetVertical
                )
            };
        }

        return match ($this->BeforeLabelPosition) {
            'bottomLeft' => sprintf(
                'left: %d%%; bottom: %d%%',
                $this->BeforeLabelOffsetHorizontal,
                $this->BeforeLabelOffsetVertical
            ),
            'topLeft' => sprintf(
                'left: %d%%; top: %d%%',
                $this->BeforeLabelOffsetHorizontal,
                $this->BeforeLabelOffsetVertical
            ),
            'middleLeft' => sprintf(
                'left: %d%%; top: 50%%; transform: translateY(-50%%);',
                $this->BeforeLabelOffsetHorizontal
            ),
        };
    }

    /**
     * @return string
     */
    public function getAfterLabelStyle(): string
    {
        if ($this->SliderDirection === 'vertical') {
            return match ($this->AfterLabelPositionVertical) {
                'left' => sprintf(
                    'left: %d%%; bottom: %d%%',
                    $this->AfterLabelOffsetHorizontal,
                    $this->AfterLabelOffsetVertical
                ),
                'center' => sprintf(
                    'left: 50%%; transform: translateX(-50%%); bottom: %d%%',
                    $this->AfterLabelOffsetVertical
                ),
                'right' => sprintf(
                    'right: %d%%; bottom: %d%%',
                    $this->AfterLabelOffsetHorizontal,
                    $this->AfterLabelOffsetVertical
                )
            };
        }

        return match ($this->AfterLabelPosition) {
            'bottomRight' => sprintf(
                'right: %d%%; bottom: %d%%',
                $this->AfterLabelOffsetHorizontal,
                $this->AfterLabelOffsetVertical
            ),
            'topRight' => sprintf(
                'right: %d%%; top: %d%%',
                $this->AfterLabelOffsetHorizontal,
                $this->AfterLabelOffsetVertical
            ),
            'middleRight' => sprintf(
                'right: %d%%; top: 50%%; transform: translateY(-50%%);',
                $this->AfterLabelOffsetHorizontal
            ),
        };
    }
}
