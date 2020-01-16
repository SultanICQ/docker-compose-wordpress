<?php

class WP_Term_Grouped {
	private $_id = '';
	private $_terms = [];

	/**
	 * @var WP_Term
	 */
	private $_primary;

	public function terms() {
		// We return a copy of the array
		return array_values($this->_terms);
	}

	public function add( WP_Term $term ) {
		$this->_terms[$term->term_id] = $term;

		if ( count($this->terms() ) == 1 ) {
			$this->setPrimary($term);
		}
	}

	public function setPrimary( WP_Term $term ) {
		$this->_primary = $term;
	}

	public function getPrimary() {
		return $this->_primary;
	}

	public function setId( $newId ) {
		$this->_id = $newId;
	}

	public function getId() {
		return $this->_id;
	}

	public function hasTerm( WP_Term $term ) {
		$has = array_filter( $this->terms(), function ($v) use ($term) {
			return ($v->term_id == $term->term_id);
		} );
		return (count($has)>0);
	}

	public function clear() {
		$this->_terms = [];
	}

	public function parents() {
		$parents = [];
		foreach( $this->terms() as $term ) {
			if ( $term->parent == 0 ) {continue;}
			$parents[$term->parent] = get_term( $term->parent, $term->taxonomy );
		}
		return $parents;
	}
}