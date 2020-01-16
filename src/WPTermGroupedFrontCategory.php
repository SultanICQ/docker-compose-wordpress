<?php
class WPTermGroupedFrontCategory {
	private $taxonomy = WP_TERM_GROUPED_TAX;
	/**
	 * wp_term_grouped_plugin constructor.
	 */
	public function __construct() {
		add_action ( 'template_redirect', array(&$this,'redirect_taxonomy') );
		add_action( 'pre_get_posts', array(&$this,'remove_limit_in_taxonomy_request') );
		add_action( 'pre_get_posts', array(&$this,'query_grouped_terms_together') );
		add_filter( 'term_link', array(&$this,'rewrite_term_link_as_main_term'), 20, 3 );

//		add_filter( 'posts_request', array(&$this,'dump_request') );
	}
	function dump_request( $input ) {

		echo $input;die;

		return $input;
	}

	function rewrite_term_link_as_main_term( $link, $term, $taxonomy ) {
		if ( $taxonomy !== $this->taxonomy )
			return $link;

		$repo = new WP_Term_Grouped_Repository();
		$group = $repo->getByTerm( $term );
		if ( is_null($group) ) {
			return $link;
		}

		$main_term = $group->getPrimary();
		if ( $main_term->term_id == $term->term_id ) {
			return $link;
		}

		$link = get_term_link( $main_term );

		return $link;
	}

	function remove_limit_in_taxonomy_request($query) {
		if ( $query->is_main_query() && $query->is_tax() && ! empty( $query->query_vars[ $this->taxonomy ] ) ) {
			$query->set( 'posts_per_page', - 1 );
		}
	}

	function query_grouped_terms_together($query) {
		if ( $query->is_main_query() && $query->is_tax() && ! empty( $query->query_vars[ $this->taxonomy ] ) ) {
			$term = get_term_by('slug', $query->query_vars[ $this->taxonomy ], $this->taxonomy );
			$repo = new WP_Term_Grouped_Repository();
			$group = $repo->getByTerm( $term );

			if ( !is_null($group) ) {
				$terms = $group->terms();
				$terms_ids = array_map( function($v) { return $v->term_id; }, $terms );
				$terms_slugs = array_map( function($v) { return $v->slug; }, $terms );

				$tax_query = array( array(
					'taxonomy' => $this->taxonomy,
					'terms' => $terms_ids,
					'field' => 'term_id',
					'operator' => 'IN',
				) );

				unset( $query->query_vars['portfolio-category'] );

				$query->set('tax_query', $tax_query );

				$query->tax_query->queries = $tax_query;
				$query->tax_query->queried_terms[ $this->taxonomy ][ 'terms' ] = $terms_slugs;
			}

		}
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

