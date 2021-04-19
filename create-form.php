<?php /*Template Name: Create Form */

get_header();

?>

<?php if(is_user_logged_in()) :?>
    <div class="text-center">
        <div id="generator-container" class="container">
            <h2> Create a new form </h2>
            <form id="creatForm" name="form" action="<?php echo home_url() . "/create-form"?>" method="post" class='bg-light mt-5 px-3 pt-5'>

                <div class="row mb-3 input-field" id="fieldWrapper1">
                    <label class="col-sm-3 col-form-label ml-2">Name</label>
                    <div class="col-sm-8">
                        <input id="inputField1" name="clientName" type="text" class="form-control" id="client-name" placeholder="Your name" required>
                    </div>
                </div>
                <!-- <a class="dropdown-item" id="add-check-box" href="#">Check Box</a> -->
                <div id="generate-btn-container" style="text-align: center;" class="mt-5">
                    <input form="creatForm" value="Create Form" id="register-btn" name="submit" type="submit" class="btn btn-primary  mb-3">
                </div>

            </form>
        </div>

    </div>

<?php else : ?>
    <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
<?php endif ; ?>


<?php get_footer();?>