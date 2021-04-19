<?php /*Template Name: QR code generate page */

// if($_POST){
//     echo "<script type='text/javascript'>window.location.href='".home_url()."'</script>";
//     exit();
// }

if (isset($_POST['submit']))
    {   
        echo '<script>window.location = "'.home_url().'/qr-code'.'" </script>';
        
    }

get_header();
?>
<?php if(is_user_logged_in()) :?>
    <div class="text-center">
        <div id="generator-container" class="container mt-5 mb-5">

            <form id="generateCode" name="form" action="<?php echo home_url();?>/qr-code/" method="post" class='bg-light mt-5 px-3 pt-5'>
                <div class="row mb-3">
                    <label class="col-sm-1 col-form-label ml-3">URL</label>
                    <div class="col-sm-10">
                        <input name="inputUrl" type="text" class="form-control" id="inputUrl" placeholder="Insert the URL you want to convert">
                    </div>
                </div>

                <div class="row mt-5">
                    <label class="col-sm-1 col-form-label ml-3">Title</label>
                    <div class="col-sm-10">
                        <input name="inputTitle"  type="text" class="form-control" id="inputTitle">
                    </div>
                </div>
                
                <div id="generate-btn-container" style="text-align: center;" class="mt-5">
                    <button id="qr-generate-btn" name="submit" type="submit" class="btn btn-primary">Generate Code</button>
                </div>
            </form>

        </div>
    </div>

    <?php else : ?>
        
       <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
    <?php endif ;?>

<?php get_footer();?>