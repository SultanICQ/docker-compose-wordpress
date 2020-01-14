<?php
class WPTermGroupedFrontCategory {
	private $taxonomy = WP_TERM_GROUPED_TAX;
	/**
	 * wp_term_grouped_plugin constructor.
	 */
	public function __construct() {
		add_action ( 'template_redirect', array(&$this,'redirect_taxonomy') );
	}

	function redirect_taxonomy() {
	    if ( !is_tax($this->taxonomy) ) {
	        return;
        }

	    $term = get_queried_object();
		$repo = new WP_Term_Grouped_Repository();
		$group = $repo->getByTerm( $term );

		if ( is_null($group) ) {
		    return;
        }

		if ( empty($group->getPrimary()) ) {
		    return;
        }

		if ( $group->getPrimary()->term_id == $term->term_id ) {
		    return;
        }

		$redirect_to = get_term_link($group->getPrimary());

		wp_redirect($redirect_to, 301 );
    }
}

