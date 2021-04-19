var myUserEntity = {};
var home_url = "";
function onSuccess(googleUser) {
    // console.log('Logged in as: ' + googleUser.getBasicProfile().getName());
    // console.log("onSuccess working!");
  }

  function onFailure(error) {
    // console.log(error);
  }

  function renderButton() {
    gapi.signin2.render('my-signin2', {
      'scope': 'profile email',
      'width': 240,
      'height': 50,
      'longtitle': true,
      'theme': 'dark',
      'onsuccess': onSuccess,
      'onfailure': onFailure
    });
  }

  function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
  }

  function onLoad() {
    gapi.load('auth2', function() {
      gapi.auth2.init();
    });
  }

  function onSignIn(googleUser) {
    prompt: 'select_account';
    var profile = googleUser.getBasicProfile();
    // console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
    // console.log('Name: ' + profile.getName());
    // console.log('Image URL: ' + profile.getImageUrl());
    // console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
    var user_id = profile.getId();
    var user_name = profile.getName();
    var img_url = profile.getImageUrl();
    var user_email = profile.getEmail();
    //send the toke to back-end
    // var id_token = googleUser.getAuthResponse().id_token;
    // url = document.getElementById("get-home-url").dataset.value;
    // sendToken(id_token, url);
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data: {action: 'login_ajax_request', user_id: user_id, user_name : user_name, img_url : img_url, user_email : user_email},
      error: function(msg){
        //console.log(msg)
        alert("The Request Failed");
      } 
    }).done(function (data) {
        //console.log(data);
        var redirect_url = window.location.href.replace("/login", "");
        
        location.replace(redirect_url);
      });
       
  }

  jQuery(document).ready(function($){
    onLoad();
    $(document).on("click", '.zj-signout-btn', function(){
      console.log("User Signed Out");
      signOut();
    })
  });

function sendToken(id_token, url){

  //Via XML
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url + "/forms/", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onreadystatechange = function() {
    if (this.readyState === 4 || this.status === 200){ 
        //console.log(this.responseText); // echo from php
    }       
  };
  xhr.send('idtoken=' + id_token);
  
  //Via Cookies

}