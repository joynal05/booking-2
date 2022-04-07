<?php

class transfers_extra_item extends transfers_entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'extra_item' );	
    }
	
	public function get_price_private() {
		$price_private = $this->get_custom_field( 'price_private' );
		return isset($price_private) ? $price_private : null;
	}

	public function get_price_shared() {
		$price_shared = $this->get_custom_field( 'price_shared' );
		return isset($price_shared) ? $price_shared : null;
	}
	
	public function get_max_allowed_per_private_transfer() {
		$max_allowed_per_private_transfer = $this->get_custom_field( 'max_allowed_per_private_transfer' );
		return isset($max_allowed_per_private_transfer) ? $max_allowed_per_private_transfer : 0;
	}
	
	public function get_max_allowed_per_shared_transfer() {
		$max_allowed_per_shared_transfer = $this->get_custom_field( 'max_allowed_per_shared_transfer' );
		return isset($max_allowed_per_shared_transfer) ? $max_allowed_per_shared_transfer : 0;
	}
}