<?php

/**
 * Sample test case.
 */
class TestGroupedTerms extends WP_UnitTestCase {

	function test_can_decorate_wordpress_term() {
		$term = get_term(1);

		$grouped = new WP_Term_Grouped($term);

		$this->assertEquals( $term->term_id, $grouped->term_id );
	}

	function test_new_grouped_has_zero_elements() {
		$term = get_term(1);

		$grouped = new WP_Term_Grouped($term);

		$this->assertEmpty( $grouped->group() );
	}

	function test_when_added_first_item_to_group_the_count_says_one() {
		$mainterm = $this->createFakeWPTerm( ['term_id'=>1] );
		$secondaryterm = $this->createFakeWPTerm( ['term_id'=>2] );

		$grouped = new WP_Term_Grouped($mainterm);
		$grouped->addToGroup( $secondaryterm );

		$this->assertEquals(1, count( $grouped->group() ) );
	}

	function test_when_added_two_items_to_group_we_can_obtain_them() {
		$mainterm = $this->createFakeWPTerm( ['term_id'=>1] );
		$secondaryterm = $this->createFakeWPTerm( ['term_id'=>2] );
		$tertiaryterm = $this->createFakeWPTerm( ['term_id'=>3] );

		$grouped = new WP_Term_Grouped($mainterm);
		$grouped->addToGroup( $secondaryterm );
		$grouped->addToGroup( $tertiaryterm );

		$this->assertEquals( [2=>$secondaryterm, 3=>$tertiaryterm], $grouped->group() );
	}

	private function createFakeWPTerm( array $vars ) {
		$term = new stdClass();
		foreach( $vars as $key => $value ) {
			$term->$key = $value;
		}
		return new WP_Term($term);
	}
}
