<?php

class WP_Term_Grouped {


	/**
	 * @var WP_Term
	 */
	private $_term;

	/**
	 * @var array
	 */
	private $_group;

	public function __construct(WP_Term $term)
    {
		$this->_term = $term;
		$this->_group = [];
    }

	public function __get($property){
		if(property_exists($this->_term, $property)) {
			return $this->_term->$property;
		}
	}

	public function group() {
		return $this->_group;
	}

	public function addToGroup( WP_Term $term ):void {
		$this->_group[ $term->term_id ] = $term;
	}
}