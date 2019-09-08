/*ffrating library - custom library created by fast forms
- Used to generate Star rating and NPS rating controls
*/
(function ($) {
    "use strict";
    var FFRating, root;

    root = typeof window !== "undefined" && window !== null ? window : global;

    root.FFRating = FFRating = (function () {

        function FFRating() {
            this.show = function () {
                var $elem = this.$elem,
                    $widget,
                    $all,
                    userOptions = this.options,
                    nextAllorPreviousAll,
                    controlSpecificClass,
                    initialOption;

                // run only once
                if (!$elem.data("ffrating")) {

                    if (userOptions.initialRating) {
                        initialOption = userOptions.initialRating;
                    } else {
                        initialOption = $elem.val();
                    }
                    if (userOptions.isStar) {
                        controlSpecificClass = "star-rating-control";
                    }
                    else {
                        controlSpecificClass = "nps-rating-control";
                    }
                    $elem.data("ffrating", {

                        userOptions: userOptions,

                        // initial rating based on the OPTION value
                        currentRatingValue: initialOption,

                        // rating will be restored by calling clear method
                        originalRatingValue: initialOption,

                    });

                    $widget = $("<div />", { "class": "ff-rating-widget " + controlSpecificClass + " custom-flex-control-container" }).insertBefore($elem);

                    // create A elements that will replace OPTIONs
                    var i = parseInt(userOptions.min);
                    var data = [];
                    var maxInteger = parseInt(userOptions.max);
                    for (i; i <= maxInteger; i++) {
                        data.push(i);
                    }


                    $.each(data, function (index, itemvalue) {
                        var val, $a, $span, $iconspan;

                        val = itemvalue;
                        var currentValue = parseInt(itemvalue);
                        // create ratings - but only if val is defined
                        if (currentValue >= 0) {


                            $a = $("<a />", { href: "#", title: val, "data-rating-value": val, "data-rating-text": val });
                            if (index == 0) {
                                $span = $("<span />", { html: userOptions.minLabel, "class": "rating-label-first" });
                            }
                            else if ((index == userOptions.medium) && !userOptions.isStar) {
                                $span = $("<span />", { html: userOptions.mediumLabel, "class": "rating-label-middle" });
                            }
                            else if (index == data.length - 1) {
                                $span = $("<span />", { html: userOptions.maxLabel, "class": "rating-label-last" });
                            }
                            else {
                                $span = $("<span />", { html: "" });
                            }
                            /* adding span element to show numbers and radio button for NPS or icons for star*/
                            if (!userOptions.isStar) {
                                $iconspan = $("<span />", { html: val, "class": "nps-text" });
                                $a.append($iconspan);
                            }
                            else {
                                $iconspan = $("<span />", { html: "", "class": "star-icon" });
                                $a.append($iconspan);
                            }
                            /**/
                            $widget.append($a.append($span));
                        }

                    });

                    // append .ff-rating-current-rating div to the widget
                    if (userOptions.showSelectedRating) {
                        $widget.append($("<span />", { text: "", "class": "ff-rating-current-rating" }));
                    }

                    // first OPTION empty - allow deselecting of ratings
                    //$elem.data("ffrating").deselectable = (!$elem.find("option:first").val()) ? true : false;

                    // use different jQuery function depending on the "reverse" setting
                    if (userOptions.reverse) {
                        nextAllorPreviousAll = "nextAll";
                    } else {
                        nextAllorPreviousAll = "prevAll";
                    }

                    // additional classes for the widget


                    if (userOptions.readonly) {
                        $widget.addClass("ff-rating-readonly");
                    }

                    // rating change event
                    $widget.on("ratingchange",
                        function (event, value) {

                            // value or text undefined?
                            value = value ? value : $elem.data("ffrating").currentRatingValue;


                            // change value in source INPUT element (now hidden)
                            $elem.val(value);

                            if (userOptions.showSelectedRating) {
                                $(this).find(".ff-rating-current-rating").text(value);
                            }
                            // trigger change event on source INPUT element
                            $elem.change();

                        }).trigger("ratingchange");

                    // rating style event
                    $widget.on("ratingstyle",
                        function (event) {
                            $widget.find("a").removeClass("ff-rating-selected ff-rating-current");

                            // add classes
                            $(this).find("a[data-rating-value=\"" + $elem.data("ffrating").currentRatingValue + "\"]")
                                .addClass("ff-rating-selected ff-rating-current")[nextAllorPreviousAll]()
                                .addClass("ff-rating-selected");


                            //$elem.attr("value",$elem.data("ffrating").currentRatingValue);
                            $elem.val($elem.data("ffrating").currentRatingValue);

                        }).trigger("ratingstyle");

                    $all = $widget.find("a");

                    // fast clicks
                    $all.on("touchstart", function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        $(this).click();
                    });

                    // do not react to click events if rating is read-only
                    if (userOptions.readonly) {
                        $all.on("click", function (event) {
                            event.preventDefault();
                        });
                    }

                    // add interactions
                    if (!userOptions.readonly) {

                        $all.on("click", function (event) {
                            var $a = $(this),
                                value,
                                text;

                            event.preventDefault();

                            $all.removeClass("ff-rating-active ff-rating-selected");
                            $a.addClass("ff-rating-selected")[nextAllorPreviousAll]()
                                .addClass("ff-rating-selected");

                            value = $a.attr("data-rating-value");
                            text = $a.attr("data-rating-text");

                            // is current and deselectable?
                            if ($a.hasClass("ff-rating-current") && $elem.data("ffrating").deselectable) {
                                $a.removeClass("ff-rating-selected ff-rating-current")[nextAllorPreviousAll]()
                                    .removeClass("ff-rating-selected ff-rating-current");
                                value = "", text = "";
                            } else {
                                $all.removeClass("ff-rating-current");
                                $a.addClass("ff-rating-current");
                            }

                            // remember selected rating
                            $elem.data("ffrating").currentRatingValue = value;


                            $widget.trigger("ratingchange");

                            // onSelect callback
                            userOptions.onSelect.call(
                                this,
                                $elem.data("ffrating").currentRatingValue

                            );

                            return false;

                        });

                        // attach mouseenter/mouseleave event handlers
                        $all.on({
                            mouseenter: function () {
                                var $a = $(this);

                                $all.removeClass("ff-rating-active").removeClass("ff-rating-selected");
                                $a.addClass("ff-rating-active")[nextAllorPreviousAll]()
                                    .addClass("ff-rating-active");

                                /*$widget.trigger("ratingchange",
                                    [$a.attr("data-rating-value"), $a.attr("data-rating-text")]
                                );*/
                            }
                        });

                        $widget.on({
                            mouseleave: function () {
                                $all.removeClass("ff-rating-active");
                                $widget.trigger("ratingstyle");
                            }
                        });

                    }

                    // hide the select box
                    // $elem.hide();
                    if (!$elem.hasClass("custom-flexcontrol-offscreen")) {
                        $elem.addClass("custom-flexcontrol-offscreen");
                    }
                }
            };

            this.destroy = function () {

                var value = this.$elem.data("ffrating").currentRatingValue;

                var options = this.$elem.data("ffrating").userOptions;

                this.$elem.removeData("ffrating");
                this.$elem.removeClass("custom-flexcontrol-offscreen");
                this.$widget.off().remove();

                // show the select box
                this.$elem.show();
                $(this.$elem).removeAttributes(["data-flex-min", "data-flex-max", "data-flex-middle", "data-flex-minlabel", "data-flex-maxlabel", "data-flex-middlelabel"]);

                // onDestroy callback
                options.onDestroy.call(
                    this,
                    value
                );

            };
        }
        function safeAttr(inputElem, dataAttr, defaultValue, setDefaultIfEmpty) {
            var defaultvalue = defaultValue;
            try {
                var defaultvalue = $(inputElem).attr(dataAttr);
                if (defaultvalue === undefined) {
                    defaultvalue = "";
                }
                if (setDefaultIfEmpty && defaultvalue == "") {
                    defaultvalue = defaultValue;
                }
            }
            catch (err) {
                defaultvalue = defaultValue
                console.log("FF log (ffrating.js):" + err.message);
            }
            return defaultvalue;
        }
        /*to remove all atrributes provided by paramter as array*/
        $.fn.removeAttributes = function (only, except) {
            if (only) {
                only = $.map(only, function (item) {
                    return item.toString().toLowerCase();
                });
            };
            if (except) {
                except = $.map(except, function (item) {
                    return item.toString().toLowerCase();
                });
                if (only) {
                    only = $.grep(only, function (item, index) {
                        return $.inArray(item, except) == -1;
                    });
                };
            };
            return this.each(function () {
                var attributes;
                if (!only) {
                    attributes = $.map(this.attributes, function (item) {
                        return item.name.toString().toLowerCase();
                    });
                    if (except) {
                        attributes = $.grep(attributes, function (item, index) {
                            return $.inArray(item, except) == -1;
                        });
                    };
                } else {
                    attributes = only;
                }
                var handle = $(this);
                $.each(attributes, function (index, item) {
                    handle.removeAttr(item);
                });
            });
        };
        FFRating.prototype.init = function (options, elem) {
            var self;
            self = this;
            self.elem = elem;
            self.$elem = $(elem);
            /* preset data attributes from input field elem*/
            if (options !== undefined) {
                if(options.min===undefined){
                    options.min = safeAttr($(elem), "data-flex-min", $.fn.ffrating.defaults.min, true);
                }
                if(options.minLabel===undefined){
                    options.minLabel = safeAttr($(elem), "data-flex-minlabel", $.fn.ffrating.defaults.minLabel, false);
                }
                if(options.max===undefined){
                    options.max = safeAttr($(elem), "data-flex-max", $.fn.ffrating.defaults.max, true);
                }
                if(options.maxLabel===undefined){
                    options.maxLabel = safeAttr($(elem), "data-flex-maxlabel", $.fn.ffrating.defaults.maxLabel, false);
                }
                if(options.medium===undefined){
                    options.medium = safeAttr($(elem), "data-flex-middle", $.fn.ffrating.defaults.medium, true);
                }
                if(options.mediumLabel===undefined){
                    options.mediumLabel = safeAttr($(elem), "data-flex-middlelabel", $.fn.ffrating.defaults.mediumLabel, false);
                }
            }


            return self.options = $.extend({}, $.fn.ffrating.defaults, options);
        };

        return FFRating;

    })();

    $.fn.ffrating = function (method, options) {
        return this.each(function () {
            var plugin = new FFRating();

            // plugin works with select fields
            if (!$(this).is("input")) {
                $.error("Sorry, this plugin only works with input fields.");
            }

            // method supplied
            if (plugin.hasOwnProperty(method)) {
                plugin.init(options, this);
                if (method === "show") {
                    return plugin.show(options);
                } else {
                    if (!$(this).hasClass("custom-flexcontrol-offscreen")) {
                        $(this).addClass("custom-flexcontrol-offscreen")
                    }
                    plugin.$widget = $(this).prev(".ff-rating-widget");

                    // widget exists?
                    if (plugin.$widget && plugin.$elem.data("ffrating")) {
                        return plugin[method](options);
                    }
                }

                // no method supplied or only options supplied
            } else if (typeof method === "object" || !method) {
                options = method;
                plugin.init(options, this);
                return plugin.show();

            } else {
                $.error("Method " + method + " does not exist on jQuery.ffrating");
            }

        });
    };
    return $.fn.ffrating.defaults = {
        initialRating: null, // initial rating
        min: 0,
        max: 10,
        steps: 1,
        medium: 5,
        minLabel: "1",
        maxLabel: "10",
        mediumLabel: "5",
        isStar: true,
        showValues: false, // display rating values on the bars?
        showSelectedRating: false, // append a div with a rating to the widget?
        reverse: false, // reverse the rating?
        readonly: false, // make the rating ready-only?
        onSelect: function () {
        }, // callback fired when a rating is selected
        onClear: function () {
        }, // callback fired when a rating is cleared
        onDestroy: function () {
        } // callback fired when a widget is destroyed
    };
})(jQuery);
/*FFRATING LIB ENDS*/