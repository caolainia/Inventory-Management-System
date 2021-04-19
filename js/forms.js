// Google Form embedding function
// var count = 0
// jQuery(document).ready(function($){
//     var screen_width = $( window ).width();
//     var screen_height = $( window ).height();
//     $('#zhijie-iframe').load(function(){
//         $('#zhijie-footer').attr("class", "zhijie-big-footer");
//         $('#zhijie-iframe').attr("width", screen_width);
//         $('#zhijie-iframe').attr("height", screen_height - 48);
//         var base = $('#get-home-url').attr('class');
//         var event_id = $('#get-event-id').attr('class').replace('event-','');
//         count += 1;
//         if (count == 2) {
//             window.location.href = base + '/auction-client?event_id=' + event_id;
//             count = 0;
//         }
//     });

//     $('.zhijie-event-card').click(function() {
//         console.log("")
//         var card_id = $(this).attr('id');
//         var base = $(this).data();
//         if (card_id) {
//             id = card_id.substring(11, card_id.length);
//             window.location.href = base + '/event-detail?event_id=' + id;
//         }
//     })
// });

function enable_signature_form() {
    var canvas = document.getElementById('signature');
    var ctx = canvas.getContext("2d");
    var drawing = false;
    var prevX, prevY;
    var currX, currY;
    var signature = document.getElementsByName('signature')[0];

    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stop);
    canvas.addEventListener("mousedown", start);

    function start() {
      drawing = true;
    }

    function stop() {
      drawing = false;
      prevX = prevY = null;
      signature.value = canvas.toDataURL('image/png');
      console.log(signature.value)
    }

    function draw(e) {
      if (!drawing) {
        return;
      }
      currX = e.clientX - canvas.offsetLeft;
      currY = e.clientY - canvas.offsetTop;
      if (!prevX && !prevY) {
        prevX = currX;
        prevY = currY;
      }

      ctx.beginPath();
      ctx.moveTo(prevX, prevY);
      ctx.lineTo(currX, currY);
      ctx.strokeStyle = 'black';
      ctx.lineWidth = 2;
      ctx.stroke();
      ctx.closePath();

      prevX = currX;
      prevY = currY;
    }
}

jQuery(document).ready(function($){
    const content = document.querySelector('#content');
    const home_url = content.dataset.url;
    
    // if (document.getElementsByClassName('block-editor__typewriter')) {
    //   $("#wpadminbar").hide();
    //   $(".components-button.edit-post-fullscreen-mode-close.has-icon").hide();
    // }

    if (document.getElementById('zhijie-event-list')) {
        $(document).on("click", ".zhijie-event-card",function (){
            var eventid = $(this).data().id
            var event_type = $(this).data().type
            if (event_type == "Inspection") {
              window.location.href = home_url + '/open-detail?event_id=' + eventid;
            } else {
              window.location.href = home_url + '/event-detail?event_id=' + eventid;
            }
            
        })
    }

    if (document.getElementById('signature')) {
        enable_signature_form();
    } else if (document.getElementById("creatForm")) {
        var field = document.getElementsByClassName("input-field");
        var form = document.getElementById("creatForm");
        var editing = false;
        var newFieldId = 1;
        var currentId = 1;
        //show edit-add button group
        $( field ).mouseover(
          function() {
            jQuery.generateBtnGroup(field);
          }
        );

        //hide that button group
        $(form).click(
          function(){
            var btnGroup = document.getElementById("edit-add-group");
            //$(btnGroup).remove();
        })

        $(document).on('click', '#btn-edit-row', function(){
            
            if(editing == false){
              editing = true;

              var inputField = $(".input-field").last();
              console.log(inputField);
              console.log('event');
              $("<div id='editing-container' class='p-3'><div class='btn-group' id='edit-btn-group' role='group'><button type='button' class='btn-success m-3' id='btn-set'>Confirm</button><button type='button' class='btn-danger m-3' id='btn-cancel'>Cancel</button></div></div>").insertAfter(inputField);
             
              var row = $(this).parent().parent();
              var label = row.find("label");
              
              
              $("<div class='row mb-3' id='edit-label'></div>").insertBefore('#edit-btn-group');
              $("#edit-label").append("<label class='col-sm-3 col-form-label ml-2'>New Label</label><input id='input-lb-edit' name='fieldName' type='text' class='form-control col-sm-3 col-form-label ml-2' placeholder='Enter Label Text' required/>");
  
              $("<div class='row mb-3' id='edit-type'></div>").insertBefore('#edit-btn-group');
              $("#edit-type").append("<label class='col-sm-3 col-form-label ml-2' for='type'>Select Input Type</label>");
              $("#edit-type").append("<select name='type' id='select-type'></select>");
              $("#select-type").append("  <option value='plain'>Plain Text</option>");
              $("#select-type").append("  <option value='mobile'>Mobile</option>");
              $("#select-type").append("  <option value='email'>Email</option>");
  
              $("#edit-type").append("<label class='col-sm-3 col-form-label ml-2' for='placeholder'>Placeholder</label>");
              $("#edit-type").append("<input id='edit-placeholder' class='form-control col-sm-3 col-form-label ml-2' placeholder='Change Placeholder'/>")
  
              //label.remove();
            }

           }
        )

        $(document).on("click", "#btn-cancel", function(){

          $("#editing-container").remove();
          editing = false;

        });

        $(document).on("click", "#btn-set", function(){

          editing = false;
          
          var row = $("#btn-edit-row").parent().parent();
          var label = row.find("label");
          console.log(label.text);
          var newText = $('#input-lb-edit').val();
          //$(label).text(newText);

          var selectedInputType = $("#select-type option:selected").text();
          console.log(selectedInputType);
          var oldInput = row.find("input");
          oldInput.remove();
          let params = new Map();

          params.set('row', row);
          params.set('label', label);
          params.set('lb-input-text', newText);
          params.set('placeholder', $("#edit-placeholder").val());
          console.log($("#edit-placeholder").text());
          if(selectedInputType == "Email"){

            jQuery.setNewInputField(params,"text", "email");
          }

          else if(selectedInputType == "mobile"){
            
            jQuery.setNewInputField(params,"text", "mobile");
          }

          else{
            jQuery.setNewInputField(params,"text", "text");
          }
          
          $("#editing-container").remove();
          editing = false;
        });

        $(document).on("click", "#add-check-box", function(){

          var row = $(this).parent().parent().parent().parent();
          console.log("add-check-box");
          jQuery.addCheckBox(row);
        });
    }

    $.fn.changeElementType = function(newType) {
      var attrs = {};

      $.each(this[0].attributes, function(idx, attr) {
          attrs[attr.nodeName] = attr.nodeValue;
      });

      this.replaceWith(function() {
          return $("<" + newType + "/>", attrs).append($(this).contents());
      });
  }

  jQuery.setNewInputField = function(params, dataType, type){
    if(dataType == "text"){
      console.log(params.get("lb-input-text"));
      $(params.get("label")).text(params.get("lb-input-text"));
      var newInputField = "<div class='col-sm-8'><input class='form-control' name='" + params.get('lb-input-text').toLowerCase().replace(" ", "-") + "' type='" + type + "' placeholder='" + params.get('placeholder') + "'/></div>";
      $(newInputField).insertAfter(params.get("label"));
    }
  
    else if(dataType == "check box"){
  
    }
  
    else if(dataType == "label"){
  
    }
  }
  jQuery.addCheckBox = function(currentRow){

    $("<div class='row mb-3 input-field'><input class='m-2' type='checkbox' value='Click edit button to customize'/><label>Click edit button to customize</label></div>").insertAfter(currentRow);
    
    //Add edit-add button group to the newst added row
    var row = $(document).find('.input-field')[currentId];
    console.log(currentRow);
    console.log(row);
    jQuery.generateBtnGroup(row);
    
    currentId++;
  }

  jQuery.generateBtnGroup = function(row){
    
    if($(row).find('.btn-group').length){
      return 0;
    }

    else
    {
      var divBtnGroup = "<div class='btn-group' id='edit-add-group' role='group' aria-label='Button group with nested dropdown'></div>";

      var btnEdit = "<button id='btn-edit-row' title='Edit this item' type='button' class='btn btn-secondary  mr-2'></button>";
      
      $(row).append(divBtnGroup);
      var divDropdown = '<div class="dropdown" id="div-dropdown"></div>';
      var btnAddRow = '<button id="btn-add-row" title="Click to insert a new item" type="button" class="btn btn-secondary"></button>'
  
      $('#edit-add-group').append(divDropdown);
      $('#edit-add-group').append(btnEdit);
  
      $('#div-dropdown').append(btnAddRow);
      var divBtnGroupAdd = "<div class='btn-group' role='group' id='btn-group-add'></div>";
  
      var divDropdownContent = '<div class="dropdown-content" id="div-dropdown-content"></div>';
      var dropdownItem_1 = '<a class="dropdown-item" id="add-check-box" href="#">Check Box</a>';
      var dropdownItem_2 = '<a class="dropdown-item" id="add-textfield" href="#">Text Field</a>';
      var dropdownItem_3 = '<a class="dropdown-item" id="add-label" href="#">Label</a>';
  
      $('#btn-group-add').append(btnAddRow);
      $('#div-dropdown').append(divDropdownContent);
      $('#div-dropdown-content').append(dropdownItem_1);
      $('#div-dropdown-content').append(dropdownItem_2);
      $('#div-dropdown-content').append(dropdownItem_3);
    }


    
  }
})
