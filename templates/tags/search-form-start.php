<?php
/**
 * The template for display opening <form> tag for search.
 *
 * @see mnca_search_form_start()
 *
 * @package    CRR_Search_Courses
 * @subpackage CRR_Search_Courses/templates/tags
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * @var array $args Template list options.
 * @var string $id
 * @var string $class
 * @var string $action
 * @var string $method
 */
extract( $args );
?>

<form
	id="<?php echo $id ?>"
	class="<?php echo $class ?>"
	action="<?php echo $action ?>"
	method="<?php echo $method ?>">
