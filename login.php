<?php /* Template Name: Login */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$event_id = -1;
$operation = 0;
if(isset($_GET["redirect"]) && $_GET["redirect"] && isset($_GET["operation"]) && $_GET["operation"]) {
    $event_id = $_GET["redirect"];
    $operation = $_GET["operation"];
}
// user logged in
if (is_user_logged_in()) {
    // with redirect id, then redirect to auction operation with event id
    if ($event_id != -1) {
        echo '<script>window.location = "'.home_url().'/auction-details?event_id=' . $event_id .'" </script>';
    } else {
        echo '<script>window.location = "'.home_url(). '" </script>';
    }
}

if($_POST) 
{  
    global $wpdb;  
   
    //We shall SQL escape all inputs  
    $username = $wpdb->escape($_REQUEST['username']);  
    $password = $wpdb->escape($_REQUEST['password']); 
    if(isset($_REQUEST['rememberme'])){

        $remember = $wpdb->escape($_REQUEST['rememberme']); 
        
        $login_data['remember'] = $remember;  
            
        if($remember) $remember = "true";  
        else $remember = "false";  
    } 
   
   
    $login_data = array();  
    $login_data['user_login'] = $username;  
    $login_data['user_password'] = $password;  
   
    $user_verify = wp_signon( $login_data, false );   
   
    if ( is_wp_error($user_verify) )   
    {  
        $error_string = $user_verify->get_error_message();
        echo "<div class='alert alert-danger' role='alert'>"."Invalid login details.". $error_string ."</div>";  
       // Note, I have created a page called "Error" that is a child of the login page to handle errors. This can be anything, but it seemed a good way to me to handle errors.  
     } else
    {  
       //echo wp_get_referer();
       echo "<script type='text/javascript'>window.location.href='". home_url() ."'</script>";  
       exit();  
     }  
   
} else 
{  
   
}  

get_header()?>
<?php if(0) :?>
    <?php //echo '<script>window.location = "'.home_url().'" </script>'; ?>   
<?php else :?>
    
    <meta name="google-signin-client_id" content="44654072123-ee2358msm5otd5o35lggll4oh8tcgcjr.apps.googleusercontent.com">
    <div class="text-center">
        <div id="login-container" class="container">
            <div id="login-container-flex">
                <div class="card bg-light mt-5 shadow-sm card-md">
                    <?php $path = get_stylesheet_directory_uri()?>
                    <h4 id="login-intro-title" class="mt-2">MyRefic is Terrific</h4>
                    <h5 id="login-intro-content" class="mt-3 font-weight-normal mb-3">
                        Efficiency tools for Real Estate agents! <br><br>
                        For more information, please visit our website:
                    </h5>
                    <div id="login-intro-link"><a href="https://www.zhijievisa.com"><u>www.zhijievisa.com</u></a></div>
                    <a id="login-intro-content" class="mt-2 font-weight-normal mb-3"></a>
                    <div class="align-middle mb-3">
                        <img id="login-logo" class="card-img-top" src=<?php echo $path."/img/conveyancer_logo.png"?> alt width="72", height="72"/>
                    </div>
                    
                    <form id="login1" name="form" action="<?php echo home_url(); ?>/login/" method="post">
            
                        <h5>Username</h5>
                        <div class="input-group form-group-row justify-content-center">
                            <input class="form-control col-md-8" id="username" type="text" placeholder="Username" name="username">
                        </div>

                        <h5>Password</h5>
                        <div class="input-group form-group-row justify-content-center">
                            <input class="form-control col-md-8" id="password" type="password" placeholder="Password" name="password">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme">
                            <label class="form-check-label" for="rememberme">Remember me</label>
                        </div>
                        <div class="form-group-row mt-4 justify-content-center login-wrapper">
                            <input type="submit" name="submit" value="Login" class="btn mb-2 btn-light-outline" id="btn-login">
                            
                        </div>
                        
                        <div class="form-group-row justify-content-center">
                            <div class="google-login-wrapper">
                                <h5 class="mb-3 font-weight-normal">Or</h5>
                                <div class="g-signin2" data-onsuccess="onSignIn"></div>
                            </div>
                            <div id="privacy-footer" class="mt-2">
                                <a href="<?php echo get_home_url() . "/privacy-policy"?>">Privacy Policies</a> | <a href="<?php echo get_home_url() . "/terms-and-conditions"?>">Terms & Conditions</a>
                            </div>
                            <div id="get-home-url" data-value="<?php //echo home_url(); ?>"></div>
                        </div>

                    </form>
                    <div class="d-flex justify-content-center mb-2">
                         Don`t have an account?
                        <a href="<?php echo home_url(); ?>/sign-up/">Sign Up</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php endif ;?>

<?php get_footer()?>

