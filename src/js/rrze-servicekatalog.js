//const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    $('.checklist-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        let $checklist = $(this).parent();
        toggleDiv($checklist);
    });

    // Keyboard navigation for accordions
    $('.checklist-toggle').keydown(function(event) {
        if (event.keyCode == 32 || event.keyCode == 13) {
            event.preventDefault();
            let $checklist = $(this).parent();
            toggleDiv($checklist);
        }
    });

    function toggleDiv($checklist) {
        $($checklist).children('.checklist-toggle').toggleClass('active');
        $($checklist).children('.checklist').slideToggle();
        $($checklist).children().find('.dashicons.dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
    }

    $('a.pdf-download').click(function(event) {
        let servicesSelected = $('.pdf-select input:checked');
        let idsSelected = [];
        for (let i = 0; i < servicesSelected.length; i++) {
            idsSelected.push($(servicesSelected[i]).data('id'));
        }
        let pdfButton = $('a.pdf-download');
        let pdfUrl = pdfButton.attr('href');
        let searchParams = new URLSearchParams(pdfUrl);
        searchParams.set('services', idsSelected.toString());
        pdfButton.attr('href', '?' + searchParams.toString());
    });
});