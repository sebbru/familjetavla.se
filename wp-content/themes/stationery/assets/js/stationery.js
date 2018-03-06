/**
 * stationery.js
 *
 * Javascript used by the Toyshop theme.
 */
( function() {
	jQuery( window ).load( function() {

		if ( jQuery( window ).width() > 767 ) {

			/**
			 * The homepage product tabs
			 */

			// Create an empty `ul` for the tabs
			jQuery( '.storefront-product-section' ).first().after( '<ul class="tabs"></ul>' );

			// Create the list items based on the titles in each of the product sections
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section") .section-title' ).each( function(i) {
				var current = jQuery(this);
				current.attr( 'section' );
				jQuery( 'ul.tabs' ).append( '<li class="' + current.html().toLowerCase() + '"><a href="#section' +
					i + '">' +
					current.html() + '</a></li>' );
			});

			// Now hide the actual section titles
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section") .section-title' ).hide();

			// Give the first tab an active class
			jQuery( 'ul.tabs li:first-child a' ).addClass( 'active' );

			// Assign an id to each of the product sections for internal linking
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).each( function(i) {
				var current = jQuery(this);
				current.attr( 'id', 'section' );
				jQuery( this ).attr( 'id', 'section' + i );
			});

			// The tabs
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).hide();
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section"):first' ).show();

			jQuery( '.tabs li' ).click(function( e ) {
				e.preventDefault();
				jQuery( '.tabs li a' ).removeClass( 'active' );
				jQuery( this ).find( 'a' ).addClass( 'active' );
				jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).hide();

				var indexer = jQuery( this ).index(); //gets the current index of (this) which is .tabs li
				jQuery( '.storefront-product-section:not(".storefront-product-categories"):eq( ' + indexer + ' )' ).fadeIn(); //uses whatever index the link has to open the corresponding box
			});

		}

	});

	jQuery( document ).ready( function() {

		/**
		 * Add class to <p> element containg the call-to-action button in Parallax Hero extension.
		 */
		if ( jQuery( '.sph-hero-content' ).length ) {
			jQuery( '.sph-hero-content p' ).last().has( 'a.button' ).addClass( 'call-to-action' );
		}

	});

} )();
