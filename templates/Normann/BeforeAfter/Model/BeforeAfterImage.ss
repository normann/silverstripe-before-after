<div>
    <% if not $HideTitle %><div class="before-after-header">$Title</div><% end_if %>
    <div class="before-after-slider-wrapper">
        <% if $ShowBadge %>
            <div class="badge-wrapper" style="$BadgeWrapperStyleVariables">
                <div class="badge $BadgeShape" style="background:#{$BadgeBackgroundColor}">
                    <p style="color:#{$BadgeTextColor}">$BadgeText</p>
                </div>
            </div>
        <% end_if %>
        <div class="before-after-slider label-show-{$LabelsVisibility} $SliderDirection"
            data-direction="$SliderDirection"
            data-offset="$SliderDefaultOffset"
            data-imagecatptions="$ImageCaptionsJson"
            data-imageratio="$ImageRatio"
        >
            <div class="before-after-slider-image before">
                <% if $LabelsVisibility != 'hideLabels' %>
                    <span class="label" style="$BeforeLabelStyle">
                        $BeforeLabel
                    </span>
                <% end_if %>
                <img alt="$BeforeLabel" src="$BeforeImage.Link">
            </div>
            <div class="handler" style="$DiagonalHandlerStyle">
                <div class="handler-style">
                    <div class="handler-style-triangles"></div>
                </div>
            </div>
            <div class="before-after-slider-image after">
                <% if $LabelsVisibility != 'hideLabels' %>
                    <span class="label" style="$AfterLabelStyle">
                        $AfterLabel
                    </span>
                <% end_if %>
                <img alt="$AfterLabel" src="$AfterImage.Link">
            </div>
        </div>
        <div class="before-after-caption"></div>
    </div>
</div>
