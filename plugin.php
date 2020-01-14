<?php
/**
 * Plugin Name:     Grouped Terms
 * Description:     Allows to group terms to work as a single unit.
 * Author:          Seocom
 * Author URI:      https://seocom.agency
 * Version:         0.1.0
 *
 */

define( 'WP_TERM_GROUPED_TAX', 'portfolio-category' );

require_once 'src/WP_Term_Grouped_Repository.php';
require_once 'src/WP_Term_Grouped.php';
require_once 'src/WPTermGroupedAdminCategory.php';
require_once 'src/WPTermGroupedFrontCategory.php';

$WPTermGroupedAdminCategory = new WPTermGroupedAdminCategory();
$WPTermGroupedFrontCategory = new WPTermGroupedFrontCategory();