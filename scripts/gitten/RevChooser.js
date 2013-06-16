/**
 * Constructs a new revision chooser component.
 * 
 * @param {!jQuery}
 *            The HTML element.
 * @constructor
 * @class
 * Component to choose the revision.
 */
gitten.RevChooser = function($)
{
    this.$ = $;
    $.addClass("gitten-rev-chooser");
    $.prop("component", this);   
    this.close();
    this.getButton().on("click", this.open.bind(this));
    $.find(".type a").on("click", this.handleTypeClick.bind(this));
    this.getFilter().on("change", this.updateFilter.bind(this));
    this.getFilter().on("keyup", this.updateFilter.bind(this));
    
    // Select first tab if none is active
    if (!this.$.find("div.type.active").length)
        this.$.find("div.type").first().addClass("active");
};

/**
 * The HTML element.
 * @private
 * @type {!jQuery}
 */
gitten.RevChooser.prototype.$;

/**
 * Remembers if the revisions have already been loaded.
 * @private
 * @type {boolean}
 */
gitten.RevChooser.prototype.revisionsLoaded = false;

/**
 * Returns the component for the specified HTML element.
 * 
 * @param {!jQuery}
 *            The HTML element.
 * @return {!gitten.RevChooser}
 *            The component.
 */
gitten.RevChooser.get = function($)
{
    var component;
    
    component = $.prop("component");
    if (!component)
    {
        component = new gitten.RevChooser($);
    }
    return component;
};

/**
 * Opens the revision chooser.
 */
gitten.RevChooser.prototype.open = function()
{
    var $popup, $autoCloser;
    
    $popup = this.getPopup();
    $autoCloser = jQuery('<div class="auto-close-layer"></div>');
    $autoCloser.on("click", this.close.bind(this));
    $autoCloser.insertBefore($popup);    
    $popup.show();
    this.getButton().addClass("open");
    this.loadRevisions();
    this.getFilter().focus();
};

/**
 * Closes the revision chooser.
 */
gitten.RevChooser.prototype.close = function()
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
gitten.RevChooser.prototype.getPopup = function()
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
gitten.RevChooser.prototype.getButton = function()
{
    return this.$.find("button");
};

/**
 * Selects the specified revision type.
 * 
 * @param {string} type
 *            The revision type (branches, tags) to select.
 */
gitten.RevChooser.prototype.selectType = function(type)
{
    this.$.find(".type").removeClass("active");
    this.$.find(".type." + type).addClass("active");
};

/**
 * Handles a click on a type button.
 * 
 * @param {!jQuery.Event} event
 *            The click event.
 * @private
 */
gitten.RevChooser.prototype.handleTypeClick = function(event)
{
    this.selectType($(event.target).data("type"));
    event.stopPropagation();
    event.preventDefault();
};

/**
 * Returns the repository path.
 * 
 * @param {string}
 *            The repository path.
 * @private
 */
gitten.RevChooser.prototype.getRepoPath = function()
{
    return this.$.data("repo-path");
};

/**
 * Returns the page ID.
 * 
 * @param {string}
 *            The page ID.
 * @private
 */
gitten.RevChooser.prototype.getPageId = function()
{
    return this.$.data("page-id");
};

/**
 * Loads the revisions from the server if not already done.
 * 
 * @private
 */
gitten.RevChooser.prototype.loadRevisions = function()
{
    var lists, i, $list, type;
    
    if (this.revisionsLoaded) return;
    lists = this.$.find(".type ul");
    for (i = lists.length - 1; i >= 0; i -= 1)
    {
        $list = jQuery(lists[i]);
        $list.addClass("loading");
        type = $list.data("type");
        jQuery.get(this.getRepoPath() + "/" + type, null,
            this.receiveRevisions.bind(this, $list)); 
    }
    this.revisionsLoaded = true;
};

/**
 * Processes the received revisions.
 * 
 * @param {!jQuery} $list
 *            The revision list element.
 * @param {!Array.<string>} revisions
 *            The received revisions.
 * @private
 */
gitten.RevChooser.prototype.receiveRevisions = function($list, revisions)
{
    var i, max, $item, revision, $link;
    
    for (i = 0, max = revisions.length; i < max; i += 1)
    {
        revision = revisions[i];
        $item = jQuery("<li>");
        $link = jQuery("<a>");
        $link.attr("href", this.getRepoPath() + "/" + this.getPageId() + "/" 
            + revision);
        $item.append($link);
        $link.text(revision);
        $list.append($item);
    }
    $list.removeClass("loading");
    this.updateFilter();
};

/**
 * Returns the filter element.
 * 
 * @param {!jQuery}
 *           The filter element.
 * @private
 */
gitten.RevChooser.prototype.getFilter = function()
{
    return this.$.find("input");
};

/**
 * Updates the revision filtering.
 * 
 * @private
 */
gitten.RevChooser.prototype.updateFilter = function()
{
    var filterText, $items, i, $item, $link;
    
    filterText = this.getFilter().val();
    $items = this.$.find("li");
    for (i = $items.length - 1; i >= 0; i -= 1)
    {
        $item = jQuery($items[i]);
        $link = $item.find("a");
        $item.toggle($link.text().indexOf(filterText) >= 0);
    }
};
