define(['jquery'], function ($) {
    'use strict';

    var configurableWidgetMixin = {

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            // Initial setting of various option values
            this._initializeOptions();

            // Override defaults with URL query parameters and/or inputs values
            this._overrideDefaults();

            // Change events to check select reloads
            this._setupChangeEvents();

            // Fill state
            this._fillState();

            // Setup child and prev/next settings
            this._setChildSettings();

            // Setup/configure values to inputs
            this._configureForValues();
            // Convert select option to div
            this._convertSelectOption();
            this._test();
            $(this.element).trigger('configurable.initialized');

            return this._super();
        },
        _test: function (){
            $('.super-attribute-select').on('change', function (){
                $(this).parents('.configurable').next().find('select').css('background-color', 'red')

                _convertSelectOption();
            })
        },
        _convertSelectOption: function () {
            setTimeout(function () {
                var customSelect = $(".product-options-wrapper .control");

                customSelect.each(function () {
                    var thisCustomSelect = $(this),
                        options = thisCustomSelect.find("option"),
                        firstOptionText = options.first().text();

                    var selectedItem = $("<div></div>", {
                        class: "selected-item",
                    })
                        .appendTo(thisCustomSelect)
                        .text(firstOptionText);

                    var allItems = $("<div></div>", {
                        class: "all-items all-items-hide",
                    }).appendTo(thisCustomSelect);

                    options.each(function () {
                        var that = $(this),
                            optionText = that.text(),
                            optionValue = that.val();

                        var item = $("<div></div>", {
                            class: "item",
                            value: optionValue,
                            on: {
                                click: function () {
                                    var selectedOptionText = that.text();
                                    selectedItem
                                        .text(selectedOptionText)
                                        .removeClass("arrowanim");
                                    allItems.addClass("all-items-hide");
                                    $(this).parents(".control").find("select").val($(this).attr("value")).trigger("change");
                                },
                            },
                        })
                            .appendTo(allItems)
                            .text(optionText);
                    });
                });

                var selectedItem = $(".selected-item"),
                    allItems = $(".all-items");

                selectedItem.off("click").on("click", function (e) {
                    var currentSelectedItem = $(this),
                        currentAllItems = currentSelectedItem.next(".all-items");

                    allItems.not(currentAllItems).addClass("all-items-hide");
                    selectedItem.not(currentSelectedItem).removeClass("arrowanim");

                    currentAllItems.toggleClass("all-items-hide");
                    currentSelectedItem.toggleClass("arrowanim");

                    e.stopPropagation();
                });
                $(document).on("click", function () {
                    var opened = $(".all-items:not(.all-items-hide)"),
                        index = opened.parent().index();

                    customSelect
                        .eq(index)
                        .find(".all-items")
                        .addClass("all-items-hide");
                    customSelect
                        .eq(index)
                        .find(".selected-item")
                        .removeClass("arrowanim");
                });
            },1000);
        }

    };

    return function (targetWidget) {
        // Example how to extend a widget by mixin object
        $.widget('mage.configurable', targetWidget, configurableWidgetMixin); // the widget alias should be like for the target widget

        return $.mage.configurable; //  the widget by parent alias should be returned
    };
});
