<?php

class transfers_transport_type extends transfers_entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'transport_type' );	
    }
}