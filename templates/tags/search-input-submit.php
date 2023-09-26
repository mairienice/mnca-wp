<?php
/**
 * The template for display input type sumbit
 *
 * @see mnca_search_input_submit()
 *
 * @package    CRR_Search_Courses
 * @subpackage CRR_Search_Courses/templates/tags
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * @var array $args Template list options.
 * @var string $value
 */
extract( $args );
?>

<input type="submit" value="<?php echo $value ?>">
