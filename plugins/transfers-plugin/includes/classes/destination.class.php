<?php

class transfers_destination extends transfers_entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'destination' );	
    }
	
	public function is_parent() {
		global $wpdb;
		
		$sql = "SELECT COUNT(*) ct FROM $wpdb->posts WHERE post_parent=%d AND post_type='destination' AND post_status='publish' ";
		return $wpdb->get_var($wpdb->prepare($sql, $this->get_id())) > 0;
	}
}