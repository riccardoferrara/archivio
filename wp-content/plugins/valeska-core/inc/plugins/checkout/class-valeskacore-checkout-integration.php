<?php

if ( ! class_exists( 'ValeskaCore_Checkout_Integration' ) ) {
	class ValeskaCore_Checkout_Integration {
		private static $instance;

		public function __construct() {

			if ( qode_framework_is_installed( 'woocommerce' ) ) {
				// Include files
				$this->include_files();

				// Override default WooCommerce hooks
				$this->override_hooks();
			}
		}

		/**
		 * @return ValeskaCore_Checkout_Integration
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function include_files() {
			include_once VALESKA_CORE_PLUGINS_PATH . '/checkout/payment/class-wc-product-qodef.php';
			include_once VALESKA_CORE_PLUGINS_PATH . '/checkout/payment/class-wc-qodef-data-store-cpt.php';
			include_once VALESKA_CORE_PLUGINS_PATH . '/checkout/payment/class-wc-order-item-qodef.php';
			include_once VALESKA_CORE_PLUGINS_PATH . '/checkout/payment/class-wc-order-item-qodef-data-store.php';
		}

		function override_hooks() {
			add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
			add_filter( 'woocommerce_cart_item_class', array( $this, 'checkout_item_classes' ), 10, 4 );
			add_filter( 'woocommerce_order_item_class', array( $this, 'order_item_classes' ), 10, 2 );
			add_filter( 'woocommerce_data_stores', array( $this, 'data_store_integration' ), 10, 1 );
			add_filter( 'woocommerce_product_type_query', array( $this, 'data_store_post_type_override' ), 10, 2 );
			add_filter( 'woocommerce_order_type_to_group', array( $this, 'data_store_post_type_to_order_type_group' ), 10, 1 );
			add_filter( 'woocommerce_checkout_create_order_line_item_object', array( $this, 'order_line_item_object_override' ), 10, 4 );
			add_filter( 'woocommerce_get_order_item_classname', array( $this, 'order_item_classname_override' ), 10, 3 );
			add_filter( 'woocommerce_get_items_key', array( $this, 'order_item_override' ), 10, 2 );
			add_filter( 'woocommerce_order_get_items', array( $this, 'order_item_types_global_extend' ), 10, 3 );
		}

		public function supported_post_types() {
			return apply_filters( 'valeska_core_filter_checkout_integration_post_types', array() );
		}

		public function product_class( $classname, $product_type, $post_type, $product_id ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) && in_array( $post_type, $supported_types, true ) ) {
				$classname = 'WC_Product_' . $this->transform_class_name( $post_type );
			}

			return $classname;
		}

		public function data_store_integration( $data_stores ) {
			$custom_data_stores = array();
			$supported_types    = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					$custom_data_stores[ $supported_type ]                 = 'WC_' . $this->transform_class_name( $supported_type ) . '_Data_Store_CPT';
					$custom_data_stores[ 'order-item-' . $supported_type ] = 'WC_Order_Item_' . $this->transform_class_name( $supported_type ) . '_Data_Store';
				}
			}

			return array_merge( $data_stores, $custom_data_stores );
		}

		public function checkout_item_classes( $classes, $cart_item, $cart_item_key ) {
			$classes .= ' qodef-product-type-' . get_post_type( $cart_item['product_id'] );

			return $classes;
		}

		public function order_item_classes( $classes, $item ) {
			$classes .= ' qodef-product-type-' . get_post_type( $item['product_id'] );

			return $classes;
		}

		public function data_store_post_type_override( $classname, $product_id ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					if ( get_post_type( $product_id ) === $supported_type ) {
						return $supported_type;
					}
				}
			}

			return false;
		}

		public function data_store_post_type_to_order_type_group( $order_groups ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					$type_edited                     = str_replace( '-', '_', $supported_type );
					$order_groups[ $supported_type ] = $type_edited . '_lines';
				}
			}

			return $order_groups;
		}

		public function order_line_item_object_override( $order_item, $cart_item_key, $values, $order ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					if ( get_post_type( $values['product_id'] ) === $supported_type ) {
						$classname  = 'WC_Order_Item_' . $this->transform_class_name( $supported_type );
						$order_item = new $classname;
						break;
					}
				}
			}

			return $order_item;
		}

		public function order_item_classname_override( $classname, $item_type, $id ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					if ( $item_type === $supported_type ) {
						$classname = 'WC_Order_Item_' . $this->transform_class_name( $supported_type );
						break;
					}
				}
			}

			return $classname;
		}

		public function order_item_override( $line_item, $item ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) ) {
				foreach ( $supported_types as $supported_type ) {
					$classname = 'WC_Order_Item_' . $this->transform_class_name( $supported_type );

					if ( is_a( $item, $classname ) ) {
						$type      = str_replace( '-', '_', $supported_type );
						$line_item = $type . '_lines';
						break;
					}
				}
			}

			return $line_item;
		}

		public function order_item_types_global_extend( $items, $order, $types ) {
			$supported_types = $this->supported_post_types();

			if ( ! empty( $supported_types ) && is_array( $types ) && 1 === sizeof( $types ) && in_array( 'line_item', $types, true ) ) {
				$supported_types[] = 'line_item';
				$items             = $order->get_items( $supported_types, true );
			}

			return $items;
		}

		private function transform_class_name( $name ) {
			$name = ucfirst( str_replace( '-', '_', $name ) );
			$name = implode( '_', array_map( 'ucwords', explode( '_', $name ) ) );

			return $name;
		}
	}

	ValeskaCore_Checkout_Integration::get_instance();
}
