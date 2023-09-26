<?php
/**
 * The template for display end </form> tag, nonce field, script.
 *
 * @uses mnca_search_hidden_nonce()
 * @see  mnca_search_form_end()
 *
 * @package    CRR_Search_Courses
 * @subpackage CRR_Search_Courses/templates/tags
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<?php mnca_search_hidden_nonce() ?>
</form>

<script>
	document.querySelectorAll(".form--search").forEach(function (form) {
		form.addEventListener("submit", function (event) {
			event.preventDefault();

			let form = event.target;
			let formFields = form.querySelectorAll("select");
			let sendForm = false;
			const formData = new FormData(form);

			for (const pair of formData.entries()) {
				if (pair[0] !== '_search' && pair[1] !== '-1') {
					sendForm = true;
				}
			}

			if (sendForm) {
				formFields.forEach((field) => {
					if (field.value.trim() === "-1") {
						field.disabled = true;
					}
				});
				form.submit();
			}
		});
	})
</script>
