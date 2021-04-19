<?php /*Template Name: Open Registration Page */

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
            <h3>Inspection Registration</h3>
            <form id="registerOpen" name="form" action="<?php echo home_url()?>/open-registration-result" method="post" class='bg-light mt-5 px-3 pt-5'>
                <div class="row mb-3 mt-3">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Open Inspection Address<span style="color:red">*</span>:</label>
                    <div class="col-sm-8">
                        <input name="openAddress" type="text" class="form-control" id="openAddress" placeholder="Enter the address of the real estate for open inspection" required>
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Agent Name<span style="color:red">*</span>:</label>
                    <div class="col-sm-8">
                        <input name="agentName"  type="text" class="form-control" id="agentName" placeholder="Enter the Real Estate agent's name for the inspection" required>
                    </div>
                </div>
                <!-- Optional -->
                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left" for="dateInput">Select date:</label>
                    <div class="col-sm-8 text-left">
                        <input type="date" id="openDateInput" name="openDateInput" min="<?php echo $current_date?>">
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left" for="timeInput">Select time:</label>
                    <div class="col-sm-8 text-left">
                        <input type="time" id="openTimeInput" name="openTimeInput">
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-3 col-form-label ml-2 fw-bolder text-left">Note:</label>
                    <div class="col-sm-8">
                        <input name="openNote"  type="text" class="form-control" id="openNote"  placeholder="Leave your notes here">
                    </div>
                </div>
                <div id="generate-btn-container" style="text-align: center;" class="my-5">
                    <input form="registerOpen" value="Create" id="create-inspection-btn" name="submit" type="submit" class="btn zhijie-button">
                </div>
            </form>
        </div>
    </div>

    <?php else : ?>
        
       <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
    <?php endif ;?>
<?php get_footer();?>