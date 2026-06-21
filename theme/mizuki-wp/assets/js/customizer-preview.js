/**
 * Customizer 实时预览脚本。
 *
 * @package Mizuki
 */
(function ( api ) {
	'use strict';

	// 主题色 hue 实时预览
	api( 'mizuki_hue', function ( value ) {
		value.bind( function ( newval ) {
			document.documentElement.style.setProperty( '--hue', String( newval ) );
			var hueStyle = document.getElementById( 'mizuki-hue' );
			if ( hueStyle ) {
				hueStyle.textContent = ':root{--hue:' + newval + ';--configHue:' + newval + ';}';
			}
		});
	});
})( wp.customize );
