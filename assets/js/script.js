$('.login-box-body form').hide();

$('.js-toggle').click(function() {
    $('.js-user').slideToggle();
    $('.js-admin').slideToggle();
});

$('.js-confirm-delete').click(function() {
    return confirm("Confirm deletion? Process is irreversible!");
});

//Initialize Select2 Elements
$(".select2").select2();

$(':input[type=number]').on('mousewheel', function(e) {
    $(this).blur();
});

$('.bootstrap-switch').bootstrapSwitch();

$(".data-table").DataTable({
    "stateSave": true,
});
$(".data-table").removeClass("hidden");
