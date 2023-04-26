/**
 * Webkul Software.
 * @category Webkul
 * @package Webkul_ZipCodeValidator
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
 var config = {
    map: {
        '*': {
            viewonproduct: 'Fahim_ZipCodeValidator/js/viewonproduct',
            'Magento_Checkout/js/view/cart/shipping-rates': 'Fahim_ZipCodeValidator/js/view/cart/shipping-rates'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Fahim_ZipCodeValidator/js/view/shipping-mixin': true
            },
        }
    }
};