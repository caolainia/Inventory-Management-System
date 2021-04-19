<?php /*Template Name: Event Registration Page */

session_start();

varify_user_loggin_status(true);
get_header();

$current_date = date("Y-m-d");
date_default_timezone_set('Australia/Adelaide');
$current_time = date("H:i");
?>
<?php if(is_user_logged_in()) :?>
    <div class="text-center mb-5">
        <div id="generator-container" class="container text-center mt-5">
            <h3>Auction Registration</h3>
            <form id="registerEvent" name="form" action="<?php echo home_url()?>/event-registration-result" method="post" class='bg-light mt-5 px-3 pt-5'>
                <h6 class="border-bottom pg-2">Mandatory Information</h6>
                <div class="row mb-3 mt-3">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Event Address<span style="color:red">*</span>:</label>
                    <div class="col-sm-8">
                        <input name="eventAddress" type="text" class="form-control" id="eventAddress" placeholder="Insert the address of this event" required>
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Host Agent<span style="color:red">*</span>:</label>
                    <div class="col-sm-8">
                        <input name="companyName"  type="text" class="form-control" id="companyName" required>
                    </div>
                </div>                
                
                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left" for="dateInput">Select date<span style="color:red">*</span>:</label>
                    <div class="col-sm-8 text-left">
                        <input type="date" id="dateInput" name="dateInput" min="<?php echo $current_date?>" required>
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left" for="timeInput">Select time<span style="color:red">*</span>:</label>
                    <div class="col-sm-8 text-left">
                        <input type="time" id="timeInput" name="timeInput" required>
                    </div>
                </div>

                <h6 class="border-bottom pg-2 mb-2 mt-5">Additional Information</h6>
                <div class="row mt-5 row-mobile">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Contact Numbers:</label>
                    <div class="col-sm-4">
                        <input name="contactNumber1"  type="text" class="form-control" id="contactNumber-1"  placeholder="Contact number #1">
                    </div>

                    <div class="col-sm-4">
                        <input name="contactNumber2"  type="text" class="form-control" id="contactNumber-2"  placeholder="Contact number #2">
                    </div>
                </div>

                <div class="row mt-5 row-mobile">
                    <label class="col-sm-3 ml-2 fw-bolder"></label>
                    <div class="col-sm-4">
                        <input name="contactNumber3"  type="text" class="form-control" id="contactNumber-3"  placeholder="Contact number #3">
                    </div>

                    <div class="col-sm-4">
                        <input name="contactNumber4"  type="text" class="form-control" id="contactNumber-4"  placeholder="Contact number #4">
                    </div>
                </div>
                
                <div class="row mt-5 row-mobile">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Additional info links:</label>
                    <div class="col-sm-8">
                        <input name="info-link1"  type="text" class="form-control" id="info-link-1"  placeholder="Attach your website url for the description of the event">
                    </div>
                </div>

                <div class="row mt-5 row-mobile">
                    <label class="col-sm-3 ml-2 fw-bolder text-left"></label>
                    <div class="col-sm-8">
                        <input name="info-link2"  type="text" class="form-control" id="info-link-2"  placeholder="Leave the link #2">
                    </div>
                </div>

                <div class="row mt-5 row-mobile">
                    <label class="col-sm-3 ml-2 fw-bolder text-left"></label>
                    <div class="col-sm-8">
                        <input name="info-link3"  type="text" class="form-control" id="info-link-3"  placeholder="Leave the link #3">
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Note:</label>
                    <div class="col-sm-8">
                        <input name="eventNote"  type="text" class="form-control" id="eventNote"  placeholder="Leave your notes here">
                    </div>
                </div>
                <div id="generate-btn-container" style="text-align: center;" class="my-5">
                    <input form="registerEvent" value="Register" id="register-btn" name="submit" type="submit" class="btn zhijie-button">
                </div>
            </form>
        </div>
    </div>

    <?php else : ?>
        
       <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
    <?php endif ;?>
<?php get_footer();?>