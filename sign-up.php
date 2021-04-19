<?php /** Template Name: Sign Up */

get_header();   
global $wpdb, $user_ID;
      $errors = array();  
      if ($user_ID) 
      {  
         
          // They're already logged in, so we bounce them back to the homepage.  
         
          #header( 'Location:' . home_url()."/redirect" );  
         
      }

    if( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
      {  
          $password = $_POST['password'];  
          $username = $_POST['username'];
          $email = $_POST['email'];
          $role = $_POST['role'];

          $new_user_id = wp_create_user( $username, $password, $email); 
          $user = new WP_User($new_user_id);

          if(is_wp_error($new_user_id)){
            $error = $new_user_id->get_error_message();
            //handle error here
            echo "<div class='alert alert-danger' role='alert'>".$error."</div>";
          }else{
            $success = 1;
            echo "success";
            echo "<script type='text/javascript'>window.location.href='". home_url() ."/login" ."'</script>";  
            //handle successful creation here
          }

          if($role == 'agent'){
            $user->remove_role('subscriber');
            $user->add_role('author');
          }

          $user_meta=get_userdata($user_id);

          echo $user_roles=$user_meta->roles; //array of roles the user is part of.
      }
      
?>  
<div class="row" style="margin-left:10vw">
    <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

              <div class="form-group">
              
                  <label for="username" class="required">User Name:</label>
                  <input class="form-control" type="username" id="username" name="username" required="">

                  <label for="email" pattern="^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+$" class="required">Email:</label><br>
                  <input type="email" id="email" name="email" class="form-control" aria-describedby="emailHelp">

                  <label for="password" class="required">Password:</label>
                  <input class="form-control required" type="password" id="password" name="password" required="">

                  <label for="confirm_password" class="required">Confirm Passowrd</label>
                  <input class="form-control mb-3" type="password" id="confirm_password" name="confirm_password" required="">

                  <label class="required">Select Your Role</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="buyer" value="buyer" checked>
                    <label class="form-check-label" for="buyer">
                      Buyer
                    </label>
                  </div>

                  <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="role" id="agent" value="agent">
                    <label class="form-check-label" for="agent">
                      Agent
                    </label>
                  </div>

                  <div class="login-wrapper">
                    <input class="btn btn-light-outline" type="submit" id="submitbtn" name="submit" value="Sign Up"></input>
                  </div>
              </div>
              
              <a href="<?php echo home_url(); ?>/login/">Already have an account? Click here to login</a>
              
    </form>
</div>
<?php get_footer(); ?>

  <script>
      var password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm_password");
      
      function validatePassword(){
        if(password.value != confirm_password.value) {
          confirm_password.setCustomValidity("Passwords Don't Match");
        } else {
          confirm_password.setCustomValidity('');
        }
      }
      
      password.onchange = validatePassword;
      confirm_password.onkeyup = validatePassword;

  </script> 