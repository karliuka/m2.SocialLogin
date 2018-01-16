/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
define([
    'Faonni_SocialLogin/js/view/checkout/authentication/provider/list'
],
function (providerList) {
    'use strict';

    return providerList.extend({
        /**
         * Default Config Option
         * @var {Object}
         */			
        defaults: {
            template: 'Faonni_SocialLogin/authentication/provider/list'
        }
    });
});