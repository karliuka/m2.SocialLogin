/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiComponent',
	'ko'
],
function ($, Component) {
    'use strict';
	
    return Component.extend({
        /**
         * Default Config Option
         * @var {Object}
         */			
        defaults: {
            template: 'Faonni_SocialLogin/checkout/authentication/provider/list'
        },
		
        /**
         * Enabled Flag
         * @var {Boolean}
         */		
		enabled: false,
		
        /**
         * Popup Flag
         * @var {Boolean}
         */		
		popup: false,
		
        /**
         * Provider Collection
         * @var {Array}
         */		
		providers: [],
		
        /**
         * initialize Component
         * @return {Void}
         */	
        initialize: function () {
            this._super();
            if (window[this.configSource] && window[this.configSource].sociallogin) {
                var config = window[this.configSource].sociallogin;
				this.providers = config.providers ? config.providers : [];
				this.popup = config.popup ? true : false;
				if (0 < this.providers.length) {
					this.enabled = true;
				}
            }			
        },
		
        /**
         * Check Functionality Should be Enabled
         * @return {Boolean}
         */
        isEnabled: function () {
            return this.enabled;
        },
		
        /**
         * Check Popup Mode
         * @return {Boolean}
         */
        isPopup: function () {
            return this.popup;
        },
		
		/**
		 * Retrieve Provider Collection
		* @returns {Array}
		*/			
		getCollection: function() {
			return this.providers;	
		}
    });
});
