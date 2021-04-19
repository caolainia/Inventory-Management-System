

(function($){
    'use strict';
    $(function (e) {
        
        $(document).on( 'click', '#submit-sheet-url-btn', function(e) {
            e.preventDefault();
                  $('#submit-sheet-url-btn').attr('disabled', 'disabled');
                  var url = document.getElementById('url').value;
                    jQuery.ajax({
                      type : "post",
                      dataType : "json",
                      url : myAjax.ajaxurl,
                      data : {action: "send_sheet_url", url : url},
                      error: function(msg){
                        console.log("The request failed");
                        // console.log(msg.responseText);
                        $('#submit-sheet-url-btn').removeAttr('disabled');
                        alert("Failed to submit the url either because of the URL is invalid or our service account hasn`t been added to the share list as an Editor. Please try again.");
                    } 
                }).success(function (data) {
                        //console.log(data);
                        linkSheetOnSuccess();
                    });
        });
    });
  })(jQuery);

jQuery(document).ready(function($){
  // if ($("#zj-ocr-button").length != 0){
  //   // AJAX for OCR to get key values
  //   $("#zj-ocr-button").on("click", function() {
  //     var pir = $("#zj-pir-file").data()['url'];
  //     var cot = $("#zj-cot-file").data()['url'];
  //     var e = $("#zj-e-file").data()['url'];
  //     var tax = $("#zj-tax-file").data()['url'];
  //     var water = $("#zj-water-file").data()['url'];
  //     var search = $("#zj-search-file").data()['url'];

  //     $("#zj-progress-bar").show();
  //     //update search result on textfield changes
  //     jQuery.ajax({
  //       type : "POST",
  //       dataType : "json",
  //       url : myAjax.ajaxurl,
  //       data : {
  //         action: "ocr_all_files", 
  //         pir : pir,
  //         cot:cot,
  //         e:e,
  //         tax:tax,
  //         water:water,
  //         search:search
  //       },
  //       error: function(msg){
  //         $("#zj-progress-bar").hide();
  //         console.log(msg.responseText);
  //         alert("Failed to ocr the PIR. Please try again");
  //       }  
  //     }).success(function (data) {
  //           console.log(data);
  //           $("#zj-progress-bar").attr("aria-valuenow", "50").css('width', "50%");
  //           $("#zj-progress-bar").text("Parsing Certificate of Titile");
  //     });
  //   })

  // }






  $(".zj-confirm-success").hide();
  const content = document.querySelector('#content');
  const home_url = content.dataset.url;
  //remove wp read more links
  $(".read-more").remove();

  //remove link spreadsheet btn
 // $(".zhijie-link-sheet-button").remove();
  
 //store all event ids for non-logged in users
  var all_event_ids = "";

    setInterval(function(){
      if(all_event_ids.length){
        $(".auctions-list-wrapper").load("?event_id="+ all_event_ids +" .zhijie-auctions-list");
      }
      else{
        $(".auctions-list-wrapper").load(" .zhijie-auctions-list");
      }
      
    }, 2000);
  

  setInterval(function(){
    if($('.zhijie-event-inspector').length){
      var event_id = $(".zhijie-event-inspector").attr('id');
      event_id = event_id.replace('inspector-', '');
    }
     $(".inspector-wrapper").load("../auction-details?event_id=" + event_id + " .inspector-inner");
     $(".event-details-table").load("../auction-details?event_id=" + event_id + " tr");
  }, 2000);


  $("#content").on("click", function() {
    if($("#div_card").length){
      
      $("#div_card").hide();
      isCardAppended = false;
    }
  });

  //Check text input box onChange
  jQuery('#search-textbox').on('input  propertychange paste', function() {
    var text = $(this).val();
    var length = text.length;
    if(length > 4){
      if(!$("#div_card").length){
        var div_card = '<div class="card" id="div_card"></div>';
        $("#div_textbox").append(div_card);
        
        var card_body = '<div id="div_card_body" class="card-body">';
        var card_title = '<h6 class="card-subtitle mb-2 text-start text-muted">Search Result of '+ text +'</h6><hr>';
        $("#div_card").append(card_body);
        $("#div_card_body").append(card_title);
        var ul = '<ul id="ul_card" class="list-group list-group-flush"></ul>';
        //var li = '<li class="list-group-item"></li>';
        
        $("#div_card_body").append(ul);
        //$("#ul_card").append(li);
      } else {
        $("#div_card").show();
      }

      //update search result on textfield changes
      jQuery.ajax({
        type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {action: "send_search_text", text : text},
        error: function(msg){
          console.log("The request failed");
          //console.log(msg.responseText);
          alert("Failed to find the address. Please try again");
        }  
      }).success(function (data) {
            //console.log(data);
            var values = data['values'];
            generateSearchItems(values, text);

            $(".card-subtitle").html ('Search Result of '+ text);

        });
    } else if (length == 0) {
        $("#div_card").hide();
        isCardAppended = false;
    }
  });

  //Send data to spreadsheet
  $(document).on('click', '#li-current-sheet', function() {
    var event_id = $(".zhijie-event-inspector").attr('id');
    event_id = event_id.replace('inspector-', '');
    $(".zhijie-link-sheet-button").attr('disabled', 'disabled');
    $(".zhijie-link-sheet-button").val("Loading...");

    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "send_data_to_sheet", event_id : event_id},
      error: function(msg){
        console.log("The request failed");
        console.log(msg.responseText);
        $(".zhijie-link-sheet-button").removeAttr('disabled');
        $(".zhijie-link-sheet-button").val("Link to Google Sheets");
        alert("Failed to link to the sheet either because of the URL is invalid or our service account hasn`t been added to the share list as an Editor. Please try again.");
      }
    }).success(function (data) {
        console.log(data);
        $(".zhijie-link-sheet-button").removeAttr('disabled');
        $(".zhijie-link-sheet-button").val("Link to Google Sheets");
        alert("Successfully linked data to your sheet");
    });
  });

  //Add auction to auction list
  $(document).on('click', '.btn-add-auction', function() {
    var id = $(this).attr('id');
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "add_event_to_list", id : id},
      error: function(msg){
        console.log("The request failed");
        // console.log(msg.responseText);
        alert("Failed to add the event. Please try again later");
      }
    }).success(function (data) {
        //console.log(data);
        if(data['is_logged_in']){

          $(".zhijie-auctions-list").load(" .zhijie-auctions-list");
        }
        else{
          //alert('not logged in yet');
          all_event_ids = id + "+" + all_event_ids;
          //console.log(all_event_ids);
          $(".zhijie-auctions-list").load("?event_id="+ all_event_ids + " .zhijie-auctions-list");
        }

        if($("#div_card").length){
          $("#div_card").hide();
          isCardAppended = false;
        }
    });
  });

  $(document).on('click', '.zhijie-link-sheet-button', function(e) {
    $(".dropdown-menu").show();

  });

  //When Auctions button onclick on event list page

  $(document).on('click', '.zhijie-button-outline', function(e) {
    //lastClickedButton = this;
    if($(this).hasClass("btn-outline-auctions")){
      var type = "auctions";
      $(".zhijie-list-header").text("My Auction Events");
    }
    else if($(this).hasClass("zhijie-button-outline-active")){
      var type = "inspections";
      $(".zhijie-list-header").text("My Inspection Events");
    }
    else{
      var type = "all";
      $(".zhijie-list-header").text("My Events");
    }
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "show_events_on_list", type : type},
      error: function(msg){
        //console.log("The request failed");
        // console.log(msg.responseText);
        alert("An error occurred. Please refresh the page!");
      }
    }).success(function (data) {
       // console.log(data);
        $("#list-row").remove();
        generateEventList(type, data, home_url);
    });

  });

  //var lastClickedButton = $(".btn-outline-auctions");
  //make button stay focus
  // $(document).on("click", function (e) {
    
  //   $(lastClickedButton).focus();
  // });

  //Table rows of Auctions list on click
  $(document).on('click', '.zhijie-tr-clickable', function(e) {
    var x = e.pageX;
    var y = e.pageY;
    if($('.zhijie-list-group').length){
      $('.zhijie-list-group').remove();
    }
    else{
      //var ul = '<ul class="dropdown-menu"></ul>'
      
      var ul = document.createElement('div');
      
      $('#content').append(ul);
      ul.classList.add('list-group', 'zhijie-list-group');
      
      $(ul).css({top: y + 'px', left: x + 'px'});
      var li_details = document.createElement('a');
      ul.appendChild(li_details);

      li_details.classList.add("list-group-item", "list-group-item-action", "btn-auction-details");
      $(li_details).text("View Details");
      
      var li_remove = document.createElement('a');
      ul.appendChild(li_remove);
      li_remove.classList.add("list-group-item", "list-group-item-action", "btn-remove-auction");
      var id = $(this).attr('id');
      var reg = /\d+/;
      var id = id.match(reg);

      $(li_remove).attr('id', id);
      $(li_remove).text("Remove");

      $(li_details).attr('id', id);
        
      var redirect_url = home_url +  'auction-details?event_id=' + id;
      $(li_details).attr('href', redirect_url);
    }
  });

  $(document).on('click', '.btn-remove-auction', function(){
    var id = $(this).attr('id');
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "remove_event_from_list", id : id},
      error: function(msg){
        console.log("The request failed");
        // console.log(msg.responseText);
        alert("Failed to submit the url either because of the URL is invalid or our service account hasn`t been added to the share list as an Editor. Please try again.");
      }
    }).success(function (data) {
        //console.log(data);
        if(data['is_logged_in'] == false){
          all_event_ids = all_event_ids.replace(id+"+", "");
        }
        else{

          $(".zhijie-auctions-list").load(" .zhijie-auctions-list");
          $('.zhijie-list-group').remove();
        }
    });
  });

  $(document).on('click', '.zhijie-start-event', function() {
    var id = $(this).attr("data-id");
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "start_event_show_prices", id: id},
      error: function(msg){
        console.log("The request failed");
        // console.log(msg.responseText);
        alert("Failed to start the event. Please make sure the event is valid.");
      }
    }).success(function (data) {
      //console.log(data);
      if (data.update_status == 1) {
        // show prices
        $("#zj-start-event-caption").hide();
        $("#zhijie-event-price-info").show();
        // hide button
        $(".zhijie-start-event").hide();
      }
    });
  });

  $(document).on('click', '#minus5000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 1000;
    $("#bid-price").val(nowprice);
  });
  $(document).on('click', '#minus2000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 2000;
    $("#bid-price").val(nowprice);
  });
  $(document).on('click', '#minus1000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 5000;
    $("#bid-price").val(nowprice);
  });
  $(document).on('click', '#add1000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 10000;
    $("#bid-price").val(nowprice);
  });
  $(document).on('click', '#add2000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 20000;
    $("#bid-price").val(nowprice);
  });
  $(document).on('click', '#add5000', function() {
    nowprice = parseInt($("#bid-price").val());
    if (!nowprice) nowprice = 0;
    nowprice += 50000;
    $("#bid-price").val(nowprice);
  });

  $(document).on('click', '#zhijie-confirm-bid', function() {
    var id = $(this).attr("data-id");
    var newPrice = $("#bid-price").val();
    var newGoing = $('.zhijie-going-button-list input:checked').attr("value");
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "store_new_bid", id: id, price: newPrice, going: newGoing},
      error: function(msg){
        console.log("The request failed");
        // console.log(msg.responseText);
        alert("Failed to confirm the bid. Please make sure the info is valid.");
      }
    }).success(function (data) {
      $("#nowPrice").text("$" + numberWithCommas(newPrice));
      newGoingShow = "Once";
      if (newGoing == 2) newGoingShow = "Twice";
      if (newGoing == 3) newGoingShow = "Gone";
      $("#nowGoing").text(newGoingShow);

      $(".zj-confirm-success").fadeIn().delay(2000).fadeOut();
    });
  });

  $(document).on('click', '#zhijie-end-event-while-bidding', function() {
    var id = $(this).attr("data-id");
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {action: "end_event_unshow_prices", id: id},
      error: function(msg){
        console.log("The request failed");
        // console.log(msg.responseText);
        alert("Failed to end the event. Please make sure the event is valid.");
      }
    }).success(function (data) {
      //console.log(data);
      if (data.update_status == 1) {
        var url = window.location.href.replace('/auction-operation', '');
      
        var index =  window.location.href.indexOf('auction-operation');
        var redirect_url = home_url +  '/event-detail?event_id=' + id;
        window.location.href = redirect_url;
      }
    });
  });

  // $(document).on('click', '.zhijie-auctions-list', function(){

  //Remove menu on click on any elements other than list group
  $(document).on('click', 'div', function(e){
    //check if is the element that trigered the event
    if($(e.target).is("td") == false){
      if($('.zhijie-list-group').length){
        $(".zhijie-list-group").remove();
      }
    }
    if($(e.target).is("button") == false){
      if($('.dropdown-menu').is(":visible")){
        $('.dropdown-menu').hide();
      }
    }
  });
  //Add more mobile number on click
  // $(document).on('click', '.btn-add-mobile', function(){
  //   var count = 2;
  //   var div_row = '<div class="row mt-5"></div>';
  //   var lb =  '<label class="col-sm-3 col-form-label ml-2">Contact Number #' + count + ':</label>'
  //   var div_col_5 = '<div class="col-sm-5"></div>'
  //   var div
  // });

})

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

//Pop up search result
function generateSearchItems(array, text){

  var ul = document.getElementById("ul_card");
  ul.innerHTML = '';
  if(array.length){

    array.forEach(element => {
      var li = document.createElement('li');
      li.innerHTML = "<div class='row mr-1'><div class='div_li'>" + element['Address'] + "</div><button class='btn btn-add-auction ml-1' type='button' id='"+ element['ID'] +"'>Add</button></div>";
      li.classList.add("list-group-item");
      ul.appendChild(li);
    });
  }
  else{
    var li = document.createElement('li');
    li.innerHTML = "<div class='row'><div class='div_li'>No event was found on this address</div></div>";
    li.classList.add("list-group-item");
    ul.appendChild(li);
  }
}

  var isDetailedGuideShow = false;
  function showHideDetail(){
    var target = document.getElementById("div-detailed-steps");
    if(isDetailedGuideShow == false){
      
      isDetailedGuideShow = true;
      //console.log(target);
      target.style.display = 'block';
    }
    else{
      isDetailedGuideShow = false;
      target.style.display = 'none';
    }
  }

  function openPopupWindow(){
    
    var redirect_url = content.dataset.url +  '/link-spreadsheet-guide';
    window.open(redirect_url,'popup','width=600,height=600'); 
    
    return false
  }
  function validateInput(){
    var address = document.getElementById("eventAddress").value;
    var time = document.getElementById("timeInput").value + ":00";
    var company = document.getElementById("companyName").value;
    var date = document.getElementById("dateInput").value;

    if(address != null && address != "" && time != null 
      && time != "" && company != null && company != "" 
      && date != null && date != "" ){
        return true;
    }
    else{
        return false;
    }

  }
  //verify time selection
  function verifyTime(){
    var selectorTime = document.getElementById("timeInput").value;

    var selectorDate = document.getElementById("dateInput").value;

    var today = new Date();
    var minutes = today.getMinutes();

    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    
    var date =  yyyy + '-' + mm + '-' + dd;
    //if current minutes is less than 10, add on 0 before minutes
    if(minutes < 10){
        var time = today.getHours() + ":0" + today.getMinutes();
    }
    else{
        var time = today.getHours() + ":" + today.getMinutes();
    }

    // if the date is today and the selected time is behind current time
    if(selectorTime < time && date == selectorDate){
        return false;
    } 
    else 
    {
        return true;
    }
  }

  //generate the cards in event list
  function generateEventList(type, array, home_url){

    var row = document.createElement("div");
    var  list_container = document.getElementById("list-container");
    row.classList.add("row", "p-2");
    row.setAttribute("id", "list-row");
    if(array.length){
      array.forEach(element => {
        var col = document.createElement("div");
        col.classList.add("col-md-4", "mb-3");

        var event_card = document.createElement("div");
        event_card.classList.add("card", "zhijie-event-card", "zhijie-shadow", "img-responsive");
        event_card.setAttribute("id", "event-card-"+ element["ID"]);
        event_card.setAttribute("data-value", home_url);
        event_card.setAttribute("data-type", element["event_type"]);
        event_card.setAttribute("data-id", element["ID"]);
        
        var banner = document.createElement("div");

        if(element["event_type"] == "Auction"){
          banner.classList.add("zj-auction-card-banner", "zhijie-card-header")
          banner.innerHTML = "Auction"
        }
        else{
          banner.classList.add("zj-inspection-card-banner", "zhijie-card-header")
          banner.innerHTML = "Inspection"
        }
        var img_card = document.createElement("img");
        img_card.setAttribute("src", element["thumbnail"]);
        img_card.classList.add("card-img-top");
        img_card.setAttribute("alt", element["address"]);

        var card_body = document.createElement("div");
        card_body.classList.add("card-body");
        var title_text = document.createElement("b");
        title_text.classList.add("zhijie-text");
        title_text.innerHTML = element["Address"];

        var list_group = document.createElement("ul");
        list_group.classList.add("list-group", "list-group-flush");

        var li_time = document.createElement("li");
        var li_note = document.createElement('li');
        li_time.classList.add("list-group-item");
        li_note.classList.add("list-group-item");
        var time_text = document.createElement("i");
        var note_text = document.createElement("i");
        time_text.textContent = "Time: " + element["Datetime"];
        if(element["comment"] != ""){
          note_text.textContent = element["comment"];
        }
        else{
          note_text.textContent = "No Comment";
        }

        li_note.appendChild(note_text);
        li_time.appendChild(time_text);
        
        list_group.appendChild(li_time);
        list_group.appendChild(li_note);
        card_body.appendChild(title_text);
        event_card.appendChild(banner);
        event_card.appendChild(img_card);
        event_card.appendChild(card_body);
        event_card.appendChild(list_group);
        col.appendChild(event_card);
        row.appendChild(col);
      });
    }
    else{
      var header = document.createElement("h3");
      header.textContent = "No Events found as " + type;
      row.classList.add("text-center");
      row.appendChild(header)
      
    }

    list_container.appendChild(row);
  }
  // function hideForm(){

  //   var form = document.getElementById("registerEvent");
  //   form.remove();
  // }

  // function indicateStatus(url, status){
      
  //   var div_container = document.getElementById("generator-container");

  //     if(status){
  //       var div_card = document.createElement("div");
  //       div_card.className = "card mb-4 box-shadow card-container"

  //       var img = document.createElement("img")
  //       var img_src = url + "/img/green-check.jpg"
  //       img.className = "card-img-top img-fluid thumbnail"
  //       img.src = img_src
  //       div_card.appendChild(img)
        
  //       var header = document.createElement("h4");
  //       header.textContent = "Successfully Registered event!"
  //       div_card.appendChild(header);
  
  //       var btn_view = document.createElement("a")
  //       btn_view.className = "btn btn-primary"
  //       btn_view.href = "#"
  //       btn_view.text = "View My Events"
  //       div_card.appendChild(btn_view);
  
  //       div_container.appendChild(div_card);
  //     }

  //     else{
  //         var div_warning = document.createElement("div");
          
  //         var header = document.createElement("h4");
  //         header.textContent = "Sorry, failed to register event. Please try again!";
  //         header.className = "text-danger";
  //         div_warning.appendChild(header);

  //         div_container.appendChild(div_warning);
  //     }

  // }

  function linkSheetOnSuccess() {
    var form = document.getElementById("container-form");
    form.style.display = 'none';

    var main = document.getElementById('main-container');
    var div_card = document.createElement("div");
    div_card.className = "card ml-5"

    var div_card_body = document.createElement("div");
    div_card_body.className = 'card-body';

    var header = document.createElement("h4");
    header.textContent = "Successfully submitted!";
    var p = document.createElement("p");
    p.innerHTML = "please go back to the page and press 'Link to Google Sheets' button to synchronize your data!";
    div_card_body.appendChild(header);
    div_card_body.appendChild(p);
    div_card.appendChild(div_card_body);

    main.appendChild(div_card);
  }
