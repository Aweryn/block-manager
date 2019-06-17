jQuery(function($) {
  var form = $("#action_new_block");

  $("#inputTitle").on("change", function(e) {
    var name = e.target.value;
    name = name.toLowerCase();
    name = name.replace(/ /g, "_");
    console.log(e.target.value);
    $("#inputName").val(name);
  });

  $(form).on("submit", function(e) {
    e.preventDefault();
    console.log("Sending data..");

    var form_data = $(this).serialize();

    $.ajax({
      url: $(this).attr("action"),
      data: $(this).serialize(), // form data
      type: "POST",
      beforeSend: function(xhr) {
        $(this, ".btn").html("Creating...");
      },
      success: function(data) {
        $("#response").html(data);
        console.log("Success!!");
        console.log(data);
        location.reload();
      }
    });
  });

  $(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

  $(".db_delete_block").on("click", function(e) {
    e.preventDefault();

    if (confirm("Are you sure you want to delete this block?")) {
      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: {
          action: "block_delete",
          block_name: $(this).data("name")
        },
        beforeSend: function(xhr) {
          //
        },
        success: function(data) {
          console.log("Success!!");
          console.log(data);
          location.reload();
        }
      });
    }
  });
});
