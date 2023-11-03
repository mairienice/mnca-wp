<?php
/**
 * The template for display the search reset button.
 *
 * @see \mnca_search_reset()
 *
 * @since 1.1.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP/templates/tags
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * @var array $args Template list options.
 * @var string $value Link text.
 * @var string $href URL for href.
 */
extract( $args );
?>
<a class="btn-reset" href="<?php echo $href ?>"><?php echo $value ?></a>
