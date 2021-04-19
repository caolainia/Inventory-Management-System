<?php 
/*Template Name: QR Code*/
get_header();
// $keys = array_keys($_POST);
$url = "123";
if(isset($_POST['inputUrl'])){

    $url = $_POST['inputUrl'];
}
?>

<?php if(is_user_logged_in()) :?>
    <div class="d-flex justify-content-center">
        <div class="card bg-light mt-5 shadow-sm card-md">
            <?php if($url) : ?>
                <?php echo do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' ); ?>
            
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-sm ">
                            <a  href="<?php echo home_url().'/event-registration'?>"><button class="btn-primary">Register your event here</button></a>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <h3>You haven`t generated any QR code yet or the url is unavailable. Redirecting...</h3>
                <?php echo '<script>window.location = "'.home_url().'/qr-code-generator'.'" </script>'; ?>
            <?php endif ; ?>

        </div>
    </div>

<?php else : ?>
    <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
<?php endif ; ?>

<?php get_footer();?>