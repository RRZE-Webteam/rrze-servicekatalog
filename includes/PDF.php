<?php

namespace RRZE\Servicekatalog;

use TCPDF;

class PDF {

    public function __construct() {
        add_action('init', [__CLASS__, 'printPDF']);
    }
    public static function printPDF($services) {
        if (!isset($_GET['services']))
            return;
        $logo = plugin()->getPath() . 'assets/img/logo_fau.svg';
        $services = explode(',', sanitize_text_field($_GET['services']));

        $pdf = new TCPDF();
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(get_bloginfo('name'));
        $pdf->SetTitle('RRZE Servicekatalog');
        $pdf->SetSubject('RRZE Servicekatalog');
        $pdf->SetKeywords('RRZE, Services, Dienstleistungen, FAU');

        // set margins
        $pdf->SetMargins(15, 15, 15, true);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set font
        $pdf->SetFont('helvetica', '', 10, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        // Add logo
        $pdf->SetX(12.5);
        $pdf->ImageSVG($logo, null, 0, 50, 30, '', 'N', '');
        $pdf->setXY(100, 6);
        $pdf->setFontSpacing(0.1);
        $pdf->SetTextColor(4,49,106);
        $pdf->MultiCell(90, 30, 'Friedrich-Alexander-Universität<br />Regionales Rechenzentrum Erlangen', 0, 'R', false, 1, null, null, true, 0, true);
        $pdf->setFontSpacing(0);

        // Write Content
        $pdf->SetX(15);
        $pdf->SetTextColor(4,49,106);
        $pdf->SetFontSize(20);
        $pdf->MultiCell(0,5, __('RRZE Servicekatalog', 'rrze-servicekatalog'), 0, 'L');

        foreach ($services as $id) {
            $meta = get_post_meta($id);
            $links = [];
            $links['portal']['label'] = __('Portal', 'rrze-servicekatalog');
            $links['portal']['url'] = Utils::getMeta($meta, 'url-portal');
            $links['description']['label'] = __('Service Description', 'rrze-servicekatalog');
            $links['description']['url'] = Utils::getMeta($meta, 'url-description');
            $links['tutorial']['label'] = __('Tutorial', 'rrze-servicekatalog');
            $links['tutorial']['url'] = Utils::getMeta($meta, 'url-tutorial');
            $links['video']['label'] = __('Video Tutorial', 'rrze-servicekatalog');
            $links['video']['url'] = Utils::getMeta($meta, 'url-video');

            $commitmentTerms = get_the_terms($id, 'rrze-service-commitment');
            if ($commitmentTerms) {
                $commitmentName = $commitmentTerms[0]->name;
                $commitmentColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', TRUE);
            }

            $pdf->SetTextColor(4,49,106);
            $pdf->SetFontSize(16);
            $pdf->SetY($pdf->getY() + $pdf->getFontSize());
            $pdf->MultiCell(0,0, get_the_title($id), 0, 'L');

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFontSize(10);
            $pdf->SetY($pdf->getY() + $pdf->getFontSize() / 2);
            $pdf->MultiCell(0,5, Utils::getMeta($meta, 'description'), 0, 'L', false, 1, null, null, true, 0, true);

            if ($commitmentTerms) {
                $pdf->SetY($pdf->getY() + $pdf->getFontSize() / 2);
                $pdf->MultiCell(0,5, __('Use', 'rrze-servicekatalog') . ': ' . $commitmentName, 0, 'L', false, 1, null, null, true, 0, true);
            }

            $pdf->SetY($pdf->getY() + $pdf->getFontSize() / 2);
            foreach ($links as $link) {
                if ($link['url'] != '') {
                    $pdf->MultiCell(0,5, $link['label'] . ': <a href="' . $link['url'] . '">' . $link['url'] . '</a>', 0, 'L', false, 1, null, null, true, 0, true);
                }
            }
        }

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('servicekatalog_' . date('Y-m-d') . '.pdf', 'I');
        exit;
    }
}