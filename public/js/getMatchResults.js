$(document).ready(function() {

    // Toggle visibility form.
    $("#getResults").click(function() {
        $("#toggle").slideToggle();
    });

    // Process form.
    $("form#ajaxGameResults").submit(function(e) {
        e.preventDefault();

        var formUrl = $(this).attr("action");
        var postData = $(this).serializeArray();

        $.ajax({
            url: formUrl,
            method: "get",
            data: postData,
            dataType: "json"
        }).done(function(data) {
            if (data == "failed") {
                alert("Het was niet mogelijk om te resultaten op te halen.")
            }
            else{
                addValues(data);
            }
        }).fail(function(data) {
            console.log(data);
            alert("Het was niet mogelijk om te resultaten op te halen.");
        });
    });

    var addValues = function (array) {
        // Get all rows.
        var inputs = $(".dest input[type='text']");
        var x = 0;

        // Loop through first dimension.
        for (var i = 0; i < array.length; i++) {
            for (var j = 0; j < array[i].length; j++) {
                inputs[j + x].value = array[i][j];
            }

            x += 3;
        }
    }
});