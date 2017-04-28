<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BFI_Payment' ) ) :

class BFI_Payment{
	public function __construct($order,$url, $debug = FALSE)
	{

	}

	public function getResult($param=null) {
		return false;
	}
}
endif;