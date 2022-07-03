<?php
	/**
	 * Accepts a zone name and returns its threshold for free shipping.
	 *
	 * @param $zone_name The name of the zone to get the threshold of. Case-sensitive.
	 * @return int The threshold corresponding to the zone, if there is any. If there is no such zone, or no free shipping method, null will be returned.
	 */
	function get_free_shipping_minimum( $zone_name = '') {
		if( ! isset( $zone_name ) ) return null;

		$min_amount = null;
		$zone   = null;

		$list_zones = WC_Shipping_Zones::get_zones();

		foreach( $list_zones as $current_zone ) {
			if ( $current_zone['zone_name'] == $zone_name ) {
				$zone = $current_zone;
			}
		}

		if( $zone ) {
			$shipping_methods_by_zone = $zone['shipping_methods'];
			$free_shipping_method     = null;

			foreach ( $shipping_methods_by_zone as $method ) {
				if( $method->id == 'free_shipping' ) {
					$free_shipping_method = $method;

					break;
				}
			}

			if ( $free_shipping_method ) {
				$min_amount = $free_shipping_method->min_amount;
			}
		}

		return $min_amount;
	}

	/**
		*Calculate the value for free shipping and show message
		*
		* @param string $zone_name
		* @param string $class_container
		* @param string $class_highlight
		* @return void
		*/
	function show_mensage_free_shipping($zone_name = '', $class_container = "", $class_highlight = "" ) {
		$free_shipping = '';
		$cart_subtotal = 0;

		if( ! class_exists( 'WooCommerce' ) ) 
			return $free_shipping;

		$get_free_shipping_minimum = get_free_shipping_minimum( $zone_name );
		$cart_subtotal             = ( WC()->cart->subtotal > 0 ) ? WC()->cart->subtotal : $cart_subtotal;

		if($cart_subtotal < $get_free_shipping_minimum) {
			$missing_amount_for_free_shipping = $get_free_shipping_minimum - $cart_subtotal;

			$free_shipping = sprintf(
				__('<p class="%s">Faltam %s <span class="%s">para o frete sair de graça</span><p>', 'theme_name'),
				$class_container,
				wc_price($missing_amount_for_free_shipping),
				$class_highlight
			);
		}

		return $free_shipping;
	}

	echo show_mensage_free_shipping('Brasil'); // Faltam R$00,00 para o frete sair de graça