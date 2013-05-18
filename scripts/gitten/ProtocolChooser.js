/**
 * Constructs a new protocol chooser component.
 * 
 * @param {!jQuery}
 *            The HTML element.
 * @constructor
 * @class
 * Component to choose the repository protocol.
 */
gitten.ProtocolChooser = function($)
{
    this.$ = $;
    $.addClass("gitten-protocol-chooser");
    $.prop("component", this);   
    this.close();
    this.getButton().on("click", this.open.bind(this));
    this.$.find("a").on("click", this.handleProtocolClick.bind(this));
};

/**
 * The HTML element.
 * @private
 * @type {!jQuery}
 */
gitten.ProtocolChooser.prototype.$;

/**
 * Returns the component for the specified HTML element.
 * 
 * @param {!jQuery}
 *            The HTML element.
 * @return {!gitten.ProtocolChooser}
 *            The component.
 */
gitten.ProtocolChooser.get = function($)
{
    var component;
    
    component = $.prop("component");
    if (!component)
    {
        component = new gitten.ProtocolChooser($);
    }
    return component;
};

/**
 * Opens the revision chooser.
 */
gitten.ProtocolChooser.prototype.open = function()
{
    var $popup, $autoCloser;
    
    $popup = this.getPopup();
    $popup.width(this.getButton().outerWidth());
    $autoCloser = jQuery('<div class="auto-close-layer"></div>');
    $autoCloser.on("click", this.close.bind(this));
    $autoCloser.insertBefore($popup);    
    $popup.show();
    this.getButton().addClass("open");
};

/**
 * Closes the revision chooser.
 */
gitten.ProtocolChooser.prototype.close = function()
{
    this.getButton().removeClass("open");
    this.getPopup().hide();
    this.$.find(".auto-close-layer").remove();
};

/**
 * Returns the popup window of the chooser.
 * 
 * @return {!jQuery}
 *            The popup window.
 * @private
 */
gitten.ProtocolChooser.prototype.getPopup = function()
{
    return this.$.find(".popup");
};

/**
 * Returns the button of the chooser.
 * 
 * @return {!jQuery}
 *            The button.
 * @private
 */
gitten.ProtocolChooser.prototype.getButton = function()
{
    return this.$.find("button");
};

/**
 * Returns the text field of the chooser.
 * 
 * @return {!jQuery}
 *            The text field.
 * @private
 */
gitten.ProtocolChooser.prototype.getTextField = function()
{
    return this.$.find("input");
};

/**
 * Selects the specified protocol.
 * 
 * @param {string} protocol
 *            The protocol (ssh, https, ...) to select.
 */
gitten.ProtocolChooser.prototype.selectProtocol = function(protocol)
{
    var $textField;
    
    $textField = this.getTextField();
    $textField.attr("value", $textField.data(protocol + "-uri"));
    this.getButton().text(protocol);
};

/**
 * Handles a click on a type button.
 * 
 * @param {!jQuery.Event} event
 *            The click event.
 * @private
 */
gitten.ProtocolChooser.prototype.handleProtocolClick = function(event)
{
    this.selectProtocol($(event.target).data("protocol"));
    this.close();
    event.stopPropagation();
    event.preventDefault();
};
