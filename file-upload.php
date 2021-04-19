<?php /* Template Name: Upload Page */
	get_header();

	use thiagoalessio\TesseractOCR\TesseractOCR;

	// echo (new TesseractOCR(get_stylesheet_directory()  . '/img/text.png'))->run(); 

	echo do_shortcode('[wordpress_file_upload]'); 
	


	add_filter('upload_mimes', 'custom_upload_mimes');
	function custom_upload_mimes ( $existing_mimes=array() ) {
	    // add your extension to the mimes array as below
	    $existing_mimes['zip'] = 'application/zip';
	    $existing_mimes['gz'] = 'application/x-gzip';
	    return $existing_mimes;
	}
	?>


<?php get_footer(); ?>