const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    // Close Accordions on start, except first
    $('.accordion-body').not(".accordion-body.open").not('.accordion-body.stayopen').hide();
    $('.accordion-body.open').each( function () {
        $(this).closest('.accordion-group').find('button.accordion-toggle').first().addClass('active');
    })
    $('.accordion').each(function() {
        if ($(this).find('button.expand-all').length > 0) {
            var items = $(this).find(".accordion-group");
            var open = $(this).find(".accordion-body.open");
            if (items.length == open.length) {
                $(this).find('button.expand-all').attr("data-status", 'open').data('status', 'open').html(__('Collapse All', 'rrze-elements'));
            }
        }
    });

    $('.checklist-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        var $checklist = $(this).next('checklist');
        var $name = $(this).data('name');
        toggleAccordion($accordion);
        // Put name attribute in URL path if available, else href
        if (typeof($name) !== 'undefined') {
            window.history.replaceState(null, null, '#' + $name);
        } else {
            window.history.replaceState(null, null, $accordion);
        }
    });

    // Keyboard navigation for accordions
    $('.accordion-toggle').keydown(function(event) {
        if (event.keyCode == 32) {
            var $accordion = $(this).attr('href');
            var $name = $(this).data('name');
            toggleAccordion($accordion);
            if (typeof($name) !== 'undefined') {
                window.history.replaceState(null, null, '#' + $name);
            } else {
                window.history.replaceState(null, null, $accordion);
            }
        }
    });

    function toggleAccordion($accordion) {
        var $thisgroup = $($accordion).closest('.accordion-group');
        var $othergroups = $($accordion).closest('.accordion').find('.accordion-group').not($thisgroup);
        $($othergroups).children('.accordion-heading').children(' .accordion-toggle').removeClass('active');
        $($othergroups).children('.accordion-body').not('.accordion-body.stayopen').slideUp();
        $($thisgroup).children('.accordion-heading').children('.accordion-toggle').toggleClass('active');
        $($thisgroup).children('.accordion-body').slideToggle();
        // refresh Slick Gallery
        var $slick = $($thisgroup).find("div.slick-slider");
        if ($slick.length < 0) {
            $slick.slick("refresh");
        }
    }
}