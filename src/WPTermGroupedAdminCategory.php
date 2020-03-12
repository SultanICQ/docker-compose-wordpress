<?php
class WPTermGroupedAdminCategory {
	private $taxonomy = WP_TERM_GROUPED_TAX;
	/**
	 * wp_term_grouped_plugin constructor.
	 */
	public function __construct() {
		add_action ( $this->taxonomy . '_edit_form_fields', array(&$this,'form_fields') );
		add_action ( $this->taxonomy . '_add_form_fields', array(&$this,'form_fields') );
		add_action ( 'edited_'.$this->taxonomy, array(&$this,'save_fields') );
		add_action ( 'create_'.$this->taxonomy, array(&$this,'save_fields') );
	}

	//add extra fields to category edit form callback function
	function form_fields( $term ) {    //check for existing featured ID
		// we check the name of the action because we need to have different output
		// if you have other taxonomy name, replace category with the name of your taxonomy. ex: book_add_form_fields, book_edit_form_fields
		if (current_filter() == $this->taxonomy . '_edit_form_fields') {
			$repo = new WP_Term_Grouped_Repository();
			$group = $repo->getByTerm( $term );

			$this->build_edit_description_footer_field($term, $group);
			$this->build_edit_primary_field($term, $group);
			$this->build_edit_grouped_terms_field($term, $group);
		} elseif (current_filter() == $this->taxonomy . '_add_form_fields') {
			$this->build_description_footer_field();
			$this->build_primary_field();
			$this->build_grouped_terms_field();
		}
	}

	function save_fields($term_id) {
		update_term_meta( $term_id, 'description_footer', $_POST['description_footer'] );
		$this->save_groupped_data($term_id);
	}ƒ

	function save_groupped_data( $term_id ) {
		if (!isset($_POST['grouped_term'])) {
			return;
		}
        $term = get_term($term_id);
		$repo = new WP_Term_Grouped_Repository();
		$group = $repo->getByTerm( $term );
		if ( is_null($group) ) {
			$group = new WP_Term_Grouped();
		} else {
			$group->clear();
		}

		$group->add( $term );

		if ( !empty($_POST['grouped_term']['terms']) ) {
			foreach ($_POST['grouped_term']['terms'] as $value_term_id ) {
				$termToGroupWith = get_term($value_term_id);
				$group->add( $termToGroupWith );
			}
		}

		if ( isset($_POST['grouped_term']['is_primary']) && $_POST['grouped_term']['is_primary'] ) {
			$group->setPrimary($term);
		}

		if ( count( $group->terms() )>1 )
		{
			$repo->add( $group );
		} else {
			$repo->remove( $group );
		}
    }

	function build_edit_description_footer_field($term, $group) {
	    $description = get_term_meta( $term->term_id, 'description_footer', true );
		?>
        <tr class="form-field term-description_footer-wrap">
            <th scope="row"><label for="description_footer"><?php _e( 'Description Footer' ); ?></label></th>
            <td><?php
                wp_editor( $description,
                'description_footer', [
                        'textarea_name' => 'description_footer',
                        'textarea_rows' => 20,
                        'textarea_cols' => 40,
                    ]);
            ?>
                <p class="description_footer"><?php _e('The description footer is not prominent by default; however, some themes may show it.'); ?></p></td>
        </tr>
		<?php
	}
	function build_edit_primary_field($term, $group) {
		$is_primary = false;
		if ( !is_null($group) && !empty($group->getPrimary()) ) {
			if ( $group->getPrimary()->term_id == $term->term_id ) {
				$is_primary = true;
			}
		}
		?>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label for="is_primary"><?php _e('Primary?'); ?></label></th>
			<td>
				<input name="grouped_term[is_primary]" id="is_primary" type="checkbox" value="1" size="40" aria-required="true" <?php if ($is_primary):?> checked="checked"<?php endif; ?>>
				<p class="description"><?php _e('Mark if this term is a primary term in a grouped term'); ?></p>
			</td>
		</tr>
		<?php
	}
	function build_edit_grouped_terms_field($term, $group) {

		$terms = get_terms( array(
			'taxonomy' => $this->taxonomy,
			'hide_empty' => false,
		) );

		$grouped = [];
		if ( !is_null($group) && !empty($group->terms()) ) {
			$grouped = array_map( function($v) {
				return $v->term_id;
			}, $group->terms() );
		}

		?>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label for="terms_grouped"><?php _e('Grouped with'); ?></label></th>
			<td>
				<?php foreach( $terms as $t ):
                    if ( $t->term_id == $term->term_id ) { continue; }
					$checked = '';
					if ( in_array( $t->term_id, $grouped ) ) { $checked = ' checked="checked"'; }
					?>
					<input type="checkbox" value="<?=$t->term_id?>" id="terms_grouped_<?=$t->term_id?>" name="grouped_term[terms][]"<?=$checked?>> <?=$t->name;?><br/>
				<?php endforeach; ?>
				<p class="description"><?php _e('Select the terms that define the group'); ?></p></td>
		</tr>
		<?php
	}
	function build_description_footer_field() {
		?>
        <div class="form-field term-description_footer-wrap">
            <label for="tag-description_footer"><?php _e( 'Description Footer' ); ?></label>
            <textarea name="description_footer" id="tag-description_footer" rows="5" cols="40"></textarea>
            <p><?php _e('The description footer is not prominent by default; however, some themes may show it.'); ?></p>
        </div>
		<?php
	}
	function build_primary_field() {
		?>
		<div class="form-field">
			<label for="terms_grouped"><?php _e('Primary?'); ?></label>
			<input type="checkbox" value="1" id="is_primary" name="grouped_term[is_primary]"> <?php _e('Is primary'); ?>
			<p><?php _e('Mark if this term is a primary term in a grouped term'); ?></p>
		</div>
		<?php
	}
	function build_grouped_terms_field() {
		$terms = get_terms( array(
			'taxonomy' => $this->taxonomy,
			'hide_empty' => false,
		) );
		?>
		<div class="form-field">
			<label><?php _e('Grouped with'); ?></label>
			<?php foreach( $terms as $term ):?>
				<input type="checkbox" value="<?=$term->term_id?>" id="terms_grouped_<?=$term->term_id?>" name="grouped_term[terms][]"> <?=$term->name?> &nbsp;
			<?php endforeach; ?>
			<p><?php _e('Select the terms that define the group'); ?></p>
		</div>
		<?php
	}
}

