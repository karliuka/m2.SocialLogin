/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
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
		options: {},

		/**
		* Initialize Widget
		* @returns {void}
		*/		
		_create: function() {
			
			var width = $(this.element).data('width');
			var height = $(this.element).data('height');
			var left = ($(document).width() - width)/2;
			var top = ($(document).height() - height)/2;			
			
			var url = $(this.element).attr('href');
			url += (url.indexOf('?') >= 0 ? '&' : '?') + 'display=popup';
			$(this.element).attr('href', 'javascript:void(false);');
			
			$(this.element).click(function() {
				if (typeof faonniSocialLogin !== 'undefined') faonniSocialLogin.close();
				else var faonniSocialLogin;
				faonniSocialLogin = window.open(url, "faonniSocialLogin", "width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + ",location=yes,status=yes");				  
			});		
		}
	});
 
    return $.faonni.socialLogin;
});