/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */ 
define([
    'jquery'
], 
function($) {
    "use strict";

	$.widget('faonni.socialLogin', {
		
        /**
         * Widget Config Option
         * @var {Object}
         */		
		options: {
			popupId: 'SocialLoginPopup',
			minWidth: 768
		},
		
        /**
         * Popup Object
         * @var {Object}
         */		
		popup: null,
		
		/**
		* Initialize Widget
		* @returns {void}
		*/		
		_create: function() {
			if ($(document).width() < this.options.minWidth) {
				return;
			}
			console.log('faonni.socialLogin');
			var width = $(this.element).data('width'),
				height = $(this.element).data('height'),
				left = ($(document).width() - width)/2,
				top = ($(document).height() - height)/2;			

			$(this.element).click(function() {
				this._createPopup(width, height, left, top);
			}.bind(this));		
		},
		
		/**
		* Create Popup
		* @param {float} width
		* @param {float} height
		* @param {float} left
		* @param {float} top
		* @returns {void}
		*/		
		_createPopup: function(width, height, left, top) {
			this._reset();	
			this.popup = window.open(
				this._getUrl(), 
				this.options.popupId, 
				"width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + ",location=yes,status=yes"
			);		
		},
		
		/**
		 * Retrieve of the Popup Url		 
		 * @return {string}	 
		 */		
        _getUrl: function() {
			var url = $(this.element).attr('href');
			url += (url.indexOf('?') >= 0 ? '&' : '?') + 'display=popup';
			$(this.element).attr('href', 'javascript:void(false);');
			return url;
        },
        
		/**
		 * Reset Popup 
		 * @return {void}	 
		 */		
        _reset: function() {
			if (null !== this.popup) {
				this.popup.close();
			}
        } 		
	});
 
    return $.faonni.socialLogin;
});