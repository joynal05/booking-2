<?php

class transfers_service extends transfers_entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'service' );	
    }

}