<?php

/**
 * Class WP_Term_Grouped_Repository
 * a:5:{s:32:"3d6064650d039e0b2c25494514bc0ce7";a:3:{s:2:"id";s:32:"3d6064650d039e0b2c25494514bc0ce7";s:7:"primary";i:68;s:5:"terms";a:4:{i:0;i:68;i:1;i:74;i:2;i:76;i:3;i:71;}}s:32:"871c985dfabce687ba4ec6bee47630e2";a:3:{s:2:"id";s:32:"871c985dfabce687ba4ec6bee47630e2";s:7:"primary";i:69;s:5:"terms";a:4:{i:0;i:69;i:1;i:67;i:2;i:72;i:3;i:75;}}s:32:"9f482dec3633881fbcc1a35d5a21518d";a:3:{s:2:"id";s:32:"9f482dec3633881fbcc1a35d5a21518d";s:7:"primary";i:70;s:5:"terms";a:2:{i:0;i:70;i:1;i:73;}}s:32:"003dc3ea8f3b185e0cc73fc267ad90e0";a:3:{s:2:"id";s:32:"003dc3ea8f3b185e0cc73fc267ad90e0";s:7:"primary";i:66;s:5:"terms";a:2:{i:0;i:66;i:1;i:54;}}s:32:"e7a0e7d3c0676fbb241afb51fbfd6a17";a:3:{s:2:"id";s:32:"e7a0e7d3c0676fbb241afb51fbfd6a17";s:7:"primary";i:65;s:5:"terms";a:2:{i:0;i:65;i:1;i:54;}}}
 */
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
			$term = get_term($v);
			if ( is_null($term) || is_wp_error($term) ) {
			} else {
				$group->add( get_term( $v ) );
			}
		} );

		$term = get_term($data['primary']);
		if ( is_null($term) || is_wp_error($term) ) {

		} else {
			$group->setPrimary( $term );
		}

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
	private function assureGroupId( WP_Term_Grouped $group ) {
		if ( $group->getId() == '' ) {
			$id = $this->buildIdForGroup( $group );
			$group->setId( $id );
		}
	}
}