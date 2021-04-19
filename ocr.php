<?php /* Template Name: OCR Page */
	get_header();

	use thiagoalessio\TesseractOCR\TesseractOCR;

	// echo (new TesseractOCR(get_stylesheet_directory()  . '/img/text.png'))->run(); 
	?>
	<div class="mt-5"></div>

	<div class="zj-upload-section">
		<form action="" method="post" enctype="multipart/form-data">
			<label for="PIRToUpload">Select the .zip file of PIR</label><br>
			<input type="file" name="PIRToUpload" id="PIRToUpload" required>
			<hr>

			<label for="COT1ToUpload">Select the .zip file of COT</label><br>
			<input type="file" name="COT1ToUpload" id="COT1ToUpload" required>
			<hr>

			<label for="EToUpload">Select the .zip file of Emergency</label><br>
			<input type="file" name="EToUpload" id="EToUpload" required>
			<hr>
			<label for="TaxToUpload">Select the .zip file of Land Tax</label><br>
			<input type="file" name="TaxToUpload" id="TaxToUpload" required>
			<hr>
			<label for="WaterToUpload">Select the .zip file of SA Water</label><br>
			<input type="file" name="WaterToUpload" id="WaterToUpload" required>
			<hr>
			<label for="SearchToUpload">Select the .zip file of Council Search</label><br>
			<input type="file" name="SearchToUpload" id="SearchToUpload" required>
			<hr>
			<input type="submit" value="Upload & Convert" name="submittheform">
		</form>
	</div>
	<?php
	if (isset($_FILES['PIRToUpload'])):
		echo "<div class=\"loader\"></div>";
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		global $wp_filesystem;
		WP_Filesystem();

		$content_directory = $wp_filesystem->wp_content_dir() . 'uploads/';
		$wp_filesystem->mkdir( $content_directory . 'form1' );

		$target_dir_location = $content_directory . 'form1/';

		$download  = get_stylesheet_directory_uri() . "/download.php";

		// Upload files
		if (isset($_FILES['PIRToUpload'])):
		    $pir_file = $_FILES['PIRToUpload']['name'];
		    $pir_tmp_name = $_FILES['PIRToUpload']['tmp_name'];
		    $pir_uploaded = $content_directory . 'form1/' . $pir_file;

		    if( move_uploaded_file( $pir_tmp_name, $target_dir_location.$pir_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-pir-file" data-url="<?php echo $pir_uploaded; ?>">
		        	PIT was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $pir_uploaded; ?>">
		        		<u><?php echo $pir_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the PIR file</div>
		    <?php endif;
		endif;

		if (isset($_FILES['COT1ToUpload'])):
	 		$cot_file = $_FILES['COT1ToUpload']['name'];
		    $cot_tmp_name = $_FILES['COT1ToUpload']['tmp_name'];
		    $cot_uploaded = $content_directory . 'form1/' . $cot_file;
			if( move_uploaded_file( $cot_tmp_name, $target_dir_location.$cot_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-cot-file" data-url="<?php echo $cot_uploaded; ?>">
		        	COT was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $cot_uploaded; ?>">
		        		<u><?php echo $cot_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the COT file</div>
		    <?php endif;
		endif;

		if(isset($_FILES["EToUpload"])):
		    $e_file = $_FILES['EToUpload']['name'];
		    $e_tmp_name = $_FILES['EToUpload']['tmp_name'];
		    $e_uploaded = $content_directory . 'form1/' . $e_file;
			if( move_uploaded_file( $e_tmp_name, $target_dir_location.$e_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-e-file" data-url="<?php echo $e_uploaded; ?>">
		        	Certificate of Emergency was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $e_uploaded; ?>">
		        		<u><?php echo $e_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the Certificate of Emergency file</div>
		    <?php endif;
		endif;
	    if(isset($_FILES["TaxToUpload"])):
		    $tax_file = $_FILES['TaxToUpload']['name'];
		    $tax_tmp_name = $_FILES['TaxToUpload']['tmp_name'];
		    $tax_uploaded = $content_directory . 'form1/' . $tax_file;
			if( move_uploaded_file( $tax_tmp_name, $target_dir_location.$tax_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-tax-file" data-url="<?php echo $tax_uploaded; ?>">
		        	Certificate of Tax was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $tax_uploaded; ?>">
		        		<u><?php echo $tax_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the Certificate of Tax file</div>
		    <?php endif;
		endif;
	    if(isset($_FILES["WaterToUpload"])):
		    $water_file = $_FILES['WaterToUpload']['name'];
		    $water_tmp_name = $_FILES['WaterToUpload']['tmp_name'];
		    $water_uploaded = $content_directory . 'form1/' . $water_file;
			if( move_uploaded_file( $water_tmp_name, $target_dir_location.$water_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-water-file" data-url="<?php echo $water_uploaded; ?>">
		        	Certificate of Water was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $water_uploaded; ?>">
		        		<u><?php echo $water_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the Certificate of Water file</div>
		    <?php endif;
		endif;
	    if(isset($_FILES["SearchToUpload"])):
		    $search_file = $_FILES['SearchToUpload']['name'];
		    $search_tmp_name = $_FILES['SearchToUpload']['tmp_name'];
		    $search_uploaded = $content_directory . 'form1/' . $search_file;
			if( move_uploaded_file( $search_tmp_name, $target_dir_location.$search_file ) ): ?> 
		        <div class="zj-upload-alert" id="zj-search-file" data-url="<?php echo $search_uploaded; ?>">
		        	Council Search was successfully uploaded <br>
		        	<a class="text-center" href="<?php echo $download . '?path=' . $search_uploaded; ?>">
		        		<u><?php echo $search_file;?></u>
		        	</a>
		        </div>
		    <?php else: ?>
		        <div class="zj-upload-alert">Cannot upload the Council Search file</div>
		    <?php endif;
		endif;

		// Extract ZIP files and convert

		$result = extract_all_files($pir_uploaded, $cot_uploaded, $e_uploaded, $tax_uploaded, $water_uploaded, $search_uploaded);
		
		// ready_pdf_filling();

		set_time_limit(300);
		require_once __DIR__ . '/ocr-factory/factory.php';
		$ocr_factory = new Factory();


		// call pir processor for each page in pir
		$pirdir = $target_dir_location . $result["pirdir"];
		$pir_result = array();
		if ($handle = opendir($pirdir)) {
		    while (false !== ($file = readdir($handle))) {
		        if ('.' === $file) continue;
		        if ('..' === $file) continue;
		        if (str_ends_with($file, ".png") || str_ends_with($file, ".jpg")) {
		        	$img_uri = $pirdir . "/" . $file;
			        $ocr_result = $ocr_factory->run_factory($img_uri, "pir");
			        $pir_result[] = $ocr_result;
		        }

		    }
		    closedir($handle);
		}
		// display_array($pir_result);

		// CT processor for each page in COT
		$cotdir = $target_dir_location . $result["cotdir"];
		$cot_result = array();
		if ($handle = opendir($cotdir)) {
		    while (false !== ($file = readdir($handle))) {
		        if ('.' === $file) continue;
		        if ('..' === $file) continue;
		        if (str_ends_with($file, ".png") || str_ends_with($file, ".jpg")) {
		        	$img_uri = $cotdir . "/" . $file;
			        $ocr_result = $ocr_factory->run_factory($img_uri, "cot");
			        $cot_result[] = $ocr_result;
		        }

		    }
		    closedir($handle);
		}
		// display_array($cot_result);
		
		ready_pdf_filling($pir_result, $cot_result);
		


	endif;
	?>



<?php get_footer(); ?>