<?php

class TestRepository extends WP_UnitTestCase {

	function test_empty_grouped_data_returns_array() {
		$repo = new Testable_WP_Term_Grouped_Repository();

		$data = $repo->getData();

		$this->assertEquals( [], $data );
	}

	function test_can_dehydrate_empty_term_group() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$group = new WP_Term_Grouped();

		$dehydrate = $repo->dehydrateGroup($group);

		$this->assertEquals( [
			'id' => '',
			'primary' => '',
			'terms' => []
		], $dehydrate );
	}

	function test_can_dehydrate_term_group_with_terms() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$insert1 = wp_insert_term('Term 1', 'category' );
		$insert2 = wp_insert_term('Term 2', 'category' );
		$group = new WP_Term_Grouped();
		$group->add( get_term($insert1['term_id']) );
		$group->add( get_term($insert2['term_id']) );

		$dehydrate = $repo->dehydrateGroup($group);

		$this->assertEquals( [
			'id' => '',
			'primary' => $insert1['term_id'],
			'terms' => [$insert1['term_id'],$insert2['term_id']]
		], $dehydrate );
	}

	function test_can_hydrate_term_group_with_data_array() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$insert1 = wp_insert_term('Term 1', 'category' );
		$insert2 = wp_insert_term('Term 2', 'category' );
		$group = new WP_Term_Grouped();
		$group->add( get_term($insert1['term_id']) );
		$group->add( get_term($insert2['term_id']) );

		$data = [
			'id' => '',
			'primary' => $insert1['term_id'],
			'terms' => [$insert1['term_id'],$insert2['term_id']]
		];

		$hydrate = $repo->hydrateGroup( $data );

		$this->assertEquals( $group, $hydrate );
	}

	function test_can_persist_term_group() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$insert1 = wp_insert_term('Term 1', 'category' );
		$insert2 = wp_insert_term('Term 2', 'category' );
		$group = new WP_Term_Grouped();
		$group->add( get_term($insert1['term_id']) );
		$group->add( get_term($insert2['term_id']) );

		$id = $repo->add( $group );

		$this->assertNotEmpty( $id );
	}

	function test_can_retrieve_term_group_with_a_term() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$insert1 = wp_insert_term('Term 1', 'category' );
		$insert2 = wp_insert_term('Term 2', 'category' );
		$group = new WP_Term_Grouped();
		$group->add( get_term($insert1['term_id']) );
		$group->add( get_term($insert2['term_id']) );

		$id = $repo->add( $group );

		$retrievedGroup = $repo->getByTerm( get_term($insert1['term_id']) );

		$this->assertNotEmpty( $id );
		$this->assertEquals( $id, $retrievedGroup->getId() );
	}

	function test_non_grouped_term_returns_null_group() {
		$repo = new Testable_WP_Term_Grouped_Repository();
		$insert1 = wp_insert_term('Term 1', 'category' );
		$insert2 = wp_insert_term('Term 2', 'category' );
		$insert3 = wp_insert_term('Term 3', 'category' );
		$group = new WP_Term_Grouped();
		$group->add( get_term($insert1['term_id']) );
		$group->add( get_term($insert2['term_id']) );

		$repo->add( $group );

		$retrievedGroup = $repo->getByTerm( get_term($insert3['term_id']) );

		$this->assertEmpty( $retrievedGroup );
	}
}

class Testable_WP_Term_Grouped_Repository extends WP_Term_Grouped_Repository {
	function getData() {
		return $this->data;
	}

}