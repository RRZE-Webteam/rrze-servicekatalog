//const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    $('.checklist-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        var $checklist = $(this).parent();
        toggleDiv($checklist);
    });

    // Keyboard navigation for accordions
    $('.checklist-toggle').keydown(function(event) {
        if (event.keyCode == 32 || event.keyCode == 13) {
            event.preventDefault();
            var $checklist = $(this).parent();
            toggleDiv($checklist);
        }
    });

    function toggleDiv($checklist) {
        $($checklist).children('.checklist-toggle').toggleClass('active');
        $($checklist).children('.checklist').slideToggle();
        $($checklist).children().find('.dashicons.dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
    }
});