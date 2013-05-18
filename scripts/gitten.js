/**
 * @type {Object}
 */
var gitten = {};

/**
 * Autostart components.
 */
jQuery(function($)
{
    // Register components
    $("[data-component]").each(function(index, component)
    {
        var $component, cls;
        
        $component = $(component);
        cls = eval($component.data("component"));
        cls.get($component);
    });
    
    // Show full text as tooltip when not already fully visible
    $(".shortened-text").each(function(index, span)
    {
        var width, fullWidth, oldStyle;

        width = span.offsetWidth;
        oldStyle = span.style.width;
        span.style.width = "auto";
        fullWidth = span.offsetWidth;
        span.style.width = oldStyle;
        if (fullWidth > width) span.title = span.innerText;
    });    
});