/**
 * Main scripts file for the About Page
 */

/* global tiAboutPageObject */
/* global console */

jQuery( document ).ready(
	function () {
		jQuery( '#about-tabs' ).tabs();
		handleLinkingInTabs();
		startLoader();

		/* Show required actions next to page title and tab title */
		if ( tiAboutPageObject.nr_actions_required > 0 ) {
			jQuery( '#about-tabs ul li > .recommended_actions' ).append( '<span class="badge-action-count">' + tiAboutPageObject.nr_actions_required + '</span>' );
		}

        jQuery( '.ti-about-page-required-action-button' ).click( function() {

            var plugin_slug = jQuery( this ).attr( 'data-slug' );

            jQuery.ajax(
                {
                    type: 'POST',
                    data: { action: 'update_recommended_plugins_visibility', slug: plugin_slug },
                    url: tiAboutPageObject.ajaxurl,
                    success: function(r) {
                    	alert( tiAboutPageObject.nr_actions_required );
						jQuery( '.' + plugin_slug ).hide();
                        jQuery( '#about-tabs ul li > .recommended_actions span' ).text( tiAboutPageObject.nr_actions_required );
                        jQuery( '#adminmenu .wp-submenu li a span.badge-action-count' ).text( tiAboutPageObject.nr_actions_required );
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
                    }
                }
			);
		} );

		/* Dismiss required actions */
		// jQuery( '.ti-about-page-required-action-button' ).click(
		// 	function () {
        //
		// 		var id = jQuery( this ).attr( 'id' ),
		// 			action = jQuery( this ).attr( 'data-action' );
        //
		// 		jQuery.ajax(
		// 			{
		// 				type: 'GET',
		// 				data: { key: 'value sent' },
		// 				data: { action: 'ti_about_page_dismiss_required_action', id: id, todo: action },
		// 				dataType: 'html',
		// 				url: tiAboutPageObject.my_ajaxurl,
		// 				beforeSend: function () {
		// 					jQuery( '.ti-about-page-tab-pane#actions_required h1' ).append( '<div id="temp_load" style="text-align:center"><img src="' + tiAboutPageObject.template_directory + '/assests/img/ajax-loader.gif" /></div>' );
		// 				},
		// 				success: function () {
		// 					location.reload();
		// 					jQuery( '#temp_load' ).remove();
		// 					/* Remove loading gif */
		// 				},
		// 				error: function ( jqXHR, textStatus, errorThrown ) {
		// 					console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
		// 				}
		// 			}
		// 		);
		// 	}
		// );
		// Remove activate button and replace with activation in progress button.
		jQuery( document ).on(
			'DOMNodeInserted', '.activate-now', function () {
				var activateButton = jQuery( this );
				if ( activateButton.length ) {
					var url = jQuery( activateButton ).attr( 'href' );
					if ( typeof url !== 'undefined' ) {
						// Request plugin activation.
						jQuery.ajax(
							{
								beforeSend: function () {
									jQuery( activateButton ).replaceWith( '<a class="button updating-message">' + tiAboutPageObject.activating_string + '...</a>' );
								},
								async: true,
								type: 'GET',
								url: url,
								success: function () {
									// Reload the page.
									location.reload();
								}
							}
						);
					}
				}
			}
		);
	}
);

function handleLinkingInTabs() {
	jQuery( '#about_tabs > div a[href^=\'#\']' ).on( 'click', function () {
		var index = jQuery( this ).attr( 'href' ).substr(1);
		jQuery( '[data-tab-id="' + index + '"] > a' ).click();
		return false;
	} );
}

function startLoader() {
	var loader = jQuery('.about-loader');

	setTimeout(function() {
		jQuery(loader).css('right', 0 );
	}, 1000);

	window.addEventListener("DOMContentLoaded", function(event) {
		setTimeout(function() {
			jQuery(loader).css( 'left', '100%');
			jQuery('.about-loading').removeClass('loading');
		}, 1500);
	});
}
