/**
 * Webkul Software
 * @category Webkul
 * @package  Webkul_ZipCodeValidator
 * @author   Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote'
    ],
    function (
        $,
        ko,
        quote
    ) {
        'use strict';

        var fahimMessage = '';
        return function (Shipping) {
            return Shipping.extend({
                defaults: {
                    template: 'Fahim_ZipCodeValidator/shipping'
                },
                message: ko.observable(false),
                formVisible: ko.observable(!quote.isVirtual()),

                initialize: function () {
                    this._super();
                    var self = this;

                    $('document').ready(function() {
                        self.setCustomMessage();
                    });

                    $('body').on('click', function() {
                        self.setCustomMessage();
                        $('.action-select-shipping-item, .action-save-address').on("click", function() {
                            $('body').trigger('processStart');
                            self.setCustomMessage();
                            setTimeout (function() {
                                $('body').trigger('processStop');
                            }, 1000);
                        });
                    });
                },

                setCustomMessage: function () {
                    var self = this;
                    var zipcode = quote.shippingAddress().postcode;
                    var message = '';
                    var quotelength = quote.getItems().length;
                    var url = window.location.origin;
                    var pathname = window.location.pathname;
                    var splittedPath = pathname.split(' ');
                    if (pathname.includes("checkout")) {
                        splittedPath = pathname.split('/checkout');
                    } else if (pathname.includes("quickOrder")) {
                        splittedPath = pathname.split('/quickOrder');
                    }
                    var baseurl = url + splittedPath[0];
                    var productId = [];
                    var i;
                    for (i in quote.getItems()) {
                       productId.push(parseInt(quote.getItems()[i].product_id));
                    }
                    if (!quote.shippingAddress().postcode) {
                        fahimMessage = 'Sorry, no quotes are available for this order at this time';
                        self.message(fahimMessage);
                    }
                    var messageAjax = $.ajax({
                        url : baseurl +"/zipcodevalidator/zipcode/shippingresult",
                        data : {
                            zip :quote.shippingAddress().postcode,
                            productId : productId
                        },
                        type : "GET",
                        success : function (response) {
                            if (response.hasOwnProperty('message')) {
                                self.formVisible(false);
                            } else {
                                self.formVisible(true);
                            }
                            fahimMessage = response.message;
                            self.message(fahimMessage);
                        }
                    });
                    return true;
                },
            });
        };
    }
);
