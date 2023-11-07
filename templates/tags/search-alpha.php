<?php
/**
 * The template for display alphabetical list for search.
 *
 * @see \mnca_search_alpha()
 *
 * @since 1.1.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP/templates/tags
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * @var array $args Template list options.
 * @var string $value
 */
extract( $args );
?>

<ul id="mnca-search-alpha">
	<?php foreach ( range( 'A', 'Z' ) as $char ) : ?>
		<li class="mnca-search-alpha__char <?php echo mnca_search_selected( '_alpha', strtolower( $char ), 'selected' ); ?>">
			<a class="mnca-search-alpha__link"
			   href="#<?php echo $char ?>"><?php echo $char ?></a>
		</li>
	<? endforeach; ?>
</ul>
<input id="mnca-search-alpha__datastore" type="hidden" name="_alpha" value="<?php echo $value ?>">
<script>
	document.addEventListener("DOMContentLoaded", function () {
		/** @type HTMLInputElement **/
		const mncaSearchAlphaDatastore = document.getElementById('mnca-search-alpha__datastore');
		/** @type HTMLFormElement **/
		const mncaSearchAlphaForm = mncaSearchAlphaDatastore.form;

		document.querySelectorAll(".mnca-search-alpha__link").forEach(function (link) {
			link.addEventListener("click", function (event) {
				event.preventDefault();
				mncaSearchAlphaDatastore.value = event.target.text.toLowerCase();
				mncaSearchAlphaForm.dispatchEvent(new Event('submit'));
			});
		});
	});
</script>
