<?php

class TestGroupedTerms extends WP_UnitTestCase {

	function test_can_create_an_empty_group_of_terms() {
		$group = new WP_Term_Grouped();

		$this->assertEquals( 'WP_Term_Grouped', get_class($group) );
		$this->assertEquals( [], $group->terms() );
	}

	function test_can_add_a_term_to_the_group() {
		$group = new WP_Term_Grouped();
		$term = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );

		$group->add($term);

		$this->assertEquals( [$term], $group->terms() );
	}

	function test_adding_two_times_the_same_term_is_only_added_once() {
		$group = new WP_Term_Grouped();
		$term = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );

		$group->add($term);
		$group->add($term);

		$this->assertEquals( [$term], $group->terms() );
	}

	function test_can_add_two_different_terms() {
		$group = new WP_Term_Grouped();
		$term1 = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );
		$term2 = $this->createFakeWPTerm( [
			'term_id' => 2,
			'name'    => 'Term 2',
			'slug'    => 'term-2',
		] );

		$group->add($term1);
		$group->add($term2);

		$this->assertEquals( [$term1, $term2], $group->terms() );
	}

	function test_can_mark_on_term_as_primary() {
		$group = new WP_Term_Grouped();
		$term1 = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );
		$term2 = $this->createFakeWPTerm( [
			'term_id' => 2,
			'name'    => 'Term 2',
			'slug'    => 'term-2',
		] );

		$group->add($term1);
		$group->add($term2);

		$group->setPrimary($term2);
		$primary = $group->getPrimary();

		$this->assertEquals( $term2, $primary );
	}

	function test_can_only_exist_single_primary_term() {
		$group = new WP_Term_Grouped();
		$term1 = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );
		$term2 = $this->createFakeWPTerm( [
			'term_id' => 2,
			'name'    => 'Term 2',
			'slug'    => 'term-2',
		] );

		$group->add($term1);
		$group->add($term2);

		$group->setPrimary($term2);
		$firstPrimary = $group->getPrimary();
		$group->setPrimary($term1);
		$secondPrimary = $group->getPrimary();

		$this->assertNotEquals( $firstPrimary, $secondPrimary );
	}

	function test_first_term_in_group_is_primary() {
		$group = new WP_Term_Grouped();
		$term1 = $this->createFakeWPTerm( [
			'term_id' => 1,
			'name'    => 'Term 1',
			'slug'    => 'term-1',
		] );

		$emptyGroupPrimary = $group->getPrimary();

		$group->add($term1);
		$singlePrimary = $group->getPrimary();

		$this->assertNull( $emptyGroupPrimary );
		$this->assertEquals( $term1, $singlePrimary );
	}

	private function createFakeWPTerm( array $vars ) {
		$term = new stdClass();
		foreach( $vars as $key => $value ) {
			$term->$key = $value;
		}
		return new WP_Term($term);
	}
}
