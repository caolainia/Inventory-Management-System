<?php /*Template Name: Site Instruction Page */
get_header();

global $wp;
$current_url = home_url( $wp->request );
?>

<?php if(is_user_logged_in()): ?>
    <div class="row mt-5" id="main-container">
        <div class="col-md-6" id="container-form">
            <div class="card text-center">
                <div class="card-header">
                    <h4>Step 1</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Share your sheet with us</p><br>

                    <b>Don`t forget to share your sheet with our service account and set as editor in order to synchronize your data</b><br>
                    <p>Copy our Service Account: <span class="bg-grey">zhijievisa@zhijievisa-project-1.iam.gserviceaccount.com</span></p>
     
                </div>
                <div class="card-footer text-muted">
                </div>
            </div>
            <div class="card text-center mt-5">
                <div class="card-header">
                    <h4>Step 2</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Submit your sheet URL here</p><br>
                    <p class="card-text"></p>
                    <form name="form" method="post" action="<?php echo $current_url;?>"class='bg-light mt-2 p-3'>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Google Sheet URL: </label>
                                <div class="col-sm-9">
                                    <input type="text" id="url" class="form-control" placeholder='URL' required>
                                </div>
                            </div>
                            <input type="submit" name="submit-btn" id="submit-sheet-url-btn" class="mt-5 btn btn-primary" value="Submit"></input>
                        </form>
                </div>
                <?php if(isset($_POST['submit-btn'])):?>
                    <h1>Set</h1>
                
                <?php endif ; ?>

                <div class="card-footer text-muted" id="div-show-detail" onclick="showHideDetail()">
                    <h4>Don`t know what to do? Clike here</h4>
                </div>
            </div>
            <div id="div-detailed-steps">
                <div class="card text-center mt-5">
                    <div class="card-header">
                        <h4>Step 1</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Create a new Spreadsheet in your drive
                        </p><br>
                        <img src="<?php echo get_stylesheet_directory_uri().'/img/share_sheets_guide/create-form.png'?>"/>
                    </div>
                </div>

                <div class="card text-center mt-5">
                    <div class="card-header">
                        <h4>Step 2</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Add us to your sheet and set as editor by clicking the green button at the top-right conner of a single sheet page, so that we can insert data to your spreadsheet.</p><br>
                        <p>Copy our Service Account: <span class="bg-grey">zhijievisa@zhijievisa-project-1.iam.gserviceaccount.com</span></p>
                        <img src="<?php echo get_stylesheet_directory_uri().'/img/share_sheets_guide/add-account.png'?>"/>

                    </div>
                </div>

                <div class="card text-center mt-5">
                    <div class="card-header">
                        <h4>Step 3</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Copy the url of your sheet and paste to the textbox in above</p><br>
                        <img src="<?php echo get_stylesheet_directory_uri().'/img/share_sheets_guide/copy-link.png'?>"/>
                    </div>
                </div>
            </div>
     
        </div>
    </div>
<?php else: ?>
    <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
<?php endif; ?>
