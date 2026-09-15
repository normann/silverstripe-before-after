<div class="before-after-image-block<% if $StyleVariant %> $StyleVariant<% end_if %><% if $ExtraClass %> $ExtraClass<% end_if %>"
     style="{$BoxShadowStyle} color:{$FrontgroundColor};"
>
    <% if $ShowTitle %>
        <h2 class="before-after-image-block__title">$Title</h2>
    <% end_if %>
    <% if $ShowContent %>
        <div class="layout-{$Layout}">
            <% if $Layout == 'contentTop' %>
            <div class="before-after-image-block__content">
                $Content
            </div>
            <% end_if %>
            <% if $BeforeAfterImage %>
                <% if $Layout == 'contentTop' || $Layout == 'contentBottom' %>
                    <div class="before-after-image-block__images">
                <% else_if $Layout == 'contentLeft' || $Layout == 'contentRight' %>
                    <div class="before-after-image-block__images-with-content">
                        <div class="before-after-image-block__images w-2-3rd">
                <% end_if %>
                            $BeforeAfterImage
                <% if $Layout == 'contentRight' || $Layout == 'contentLeft'%>
                        </div>
                        $Content
                    </div>
                <% else_if $Layout == 'contentTop' || $Layout == 'contentBottom' %>
                    </div>
                <% end_if %>
            <% end_if %>
            <% if $Layout == 'contentBottom' %>
                <div class="before-after-image-block__content">
                    $Content
                </div>
            <% end_if %>
        </div>
    <% else %>
        <div class="before-after-image-block__images">
            $BeforeAfterImage
        </div>
    <% end_if %>
</div>
