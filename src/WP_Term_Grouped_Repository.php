<?php

class WP_Term_Grouped_Repository {
	private $option = 'term_grouped_data';

	protected $data = [];

	public function __construct() {
		$this->load();
	}

	public function buildIdForGroup( WP_Term_Grouped $group ) {
		return md5( serialize($group) );
	}

	public function dehydrateGroup( WP_Term_Grouped $group ) {

		$primary = $group->getPrimary();
		$primary = empty($primary) ? '' : $primary->term_id;

		return [
			'id'      => $group->getId(),
			'primary' => $primary,
			'terms'   => array_map( function($v) { return $v->term_id; }, $group->terms() ),
		];
	}

	public function hydrateGroup( array $data ) {
		$group = new WP_Term_Grouped();

		$group->setId( $data['id'] );
		array_walk( $data['terms'], function($v) use ( $group ){
			$group->add( get_term( $v ) );
		} );
		$group->setPrimary( get_term($data['primary']) );

		return $group;
	}

	public function load() {
		$this->data = (array)get_option($this->option,[]);
	}

	function save() {
		update_option( $this->option, (array)$this->data );
	}

	public function add( WP_Term_Grouped $group ) {
		$this->assureGroupId( $group );
		$this->data[ $group->getId() ] = $this->dehydrateGroup($group);
		$this->save();

		return $group->getId();
	}

	public function remove( WP_Term_Grouped $group ) {
		$this->assureGroupId( $group );
		unset($this->data[ $group->getId() ]);
		$this->save();

		return $group->getId();
	}

	public function getByTerm( WP_Term $term ) {
		foreach( $this->data as $data ) {
			$group = $this->hydrateGroup( $data );
			if ( $group->hasTerm( $term ) ) {
				return $group;
			}
		}

		return null;
	}

	/**
	 * @param WP_Term_Grouped $group
	 */
	private function assureGroupId( WP_Term_Grouped $group ): void {
		if ( $group->getId() == '' ) {
			$id = $this->buildIdForGroup( $group );
			$group->setId( $id );
		}
	}
}