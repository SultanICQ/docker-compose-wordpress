<?php

class WP_Term_Grouped_Repository
{
	private $option = 'term_grouped_data';

	protected $data = [];

	public function __construct() {
		$this->load();
	}

	private function buildIdForGroup( WP_Term_Grouped $group ) {
		return md5( serialize($group) );
	}

	public function dehydrateGroup( WP_Term_Grouped $group ):array {

		$primary = $group->getPrimary();
		$primary = empty($primary) ? '' : $primary->term_id;

		return [
			'id'      => $group->getId(),
			'primary' => $primary,
			'terms'   => array_map( function($v) { return $v->term_id; }, $group->terms() ),
		];
	}

	public function hydrateGroup( array $data ):WP_Term_Grouped {
		$group = new WP_Term_Grouped();

		$group->setId( $data['id'] );
		array_walk( $data['terms'], function($v) use ( $group ){
			$group->add( get_term( $v ) );
		} );
		$group->setPrimary( get_term($data['primary']) );

		return $group;
	}

	private function load() {
		$this->data = (array)get_option($this->option,[]);
	}

	function save() {
		update_option( $this->option, (array)$this->data );
	}

	public function add( WP_Term_Grouped $group ) {
//		if ( $group->getId() == '' ) {
//			$group->setId( $this->buildIdForGroup( $group ) );
//		}
//		$this->data[ $group->getId() ] = $this->dehydrateGroup($group);
//		$this->save();
	}




	public function getByTerm( WP_Term $term ): ?WP_Term_Grouped {
		foreach( $this->data as $data ) {
			$group = WP_Term_Grouped::hydrate( $data );
			if ( $group->hasTerm( $term ) ) {
				return $group;
			}
		}

		return null;
	}
}