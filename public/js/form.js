var homeCheckbox = $('#home');
var address = $('#address');

/*
 If the page loads check if the checkbox is checked.
 */
if (homeCheckbox.is(':checked')) {
    address.hide();
}
else {
    address.show();
}

/*
 When checkbox is checked then remove address fields. Otherwise show.
 */
homeCheckbox.change(function() {
    if (this.checked) {
        address.slideUp();
    }
    else {
        address.slideDown();
    }
});

/*
Remove the alert box after x seconds.
 */
setTimeout(function() {
    $('.alert').slideUp();
}, 3000);

/*
Light up all place a player will play.
 */

var list = $('.lightUp');

$(".lightUp").mouseover(function() {

    // Fetch value of field.
    var value = $(this).text();

    // Loop through all fields.
    for (var i = 0; i < list.length; i++) {

        // Change all fields equal as value.
        list.filter(function() {
            return $(this).text() == value;
        }).addClass("info");

    }

}).mouseout(function() {
    // Remove all css.
    $(".lightUp").removeClass("info");
});



