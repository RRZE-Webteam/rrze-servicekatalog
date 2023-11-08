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
        $services = explode(',', sanitize_text_field($_GET['services']));

        $pdf = new RRZE_PDF();

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(get_bloginfo('name'));
        $pdf->SetTitle('RRZE Servicekatalog');
        $pdf->SetSubject('RRZE Servicekatalog');
        $pdf->SetKeywords('RRZE, Services, Dienstleistungen, FAU');

        // set margins
        $pdf->SetMargins(15, 40, 15, true);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30);

        // Set font
        $pdf->SetFont('helvetica', '', 10, '', true);

        // Add a page
        $pdf->AddPage('P', '', true);

        // Write Title
        $pdf->SetTextColor(40,60,122);
        $pdf->WriteHTMLCell(0, 5, null, null, '<h1 style="font-weight: normal; font-size: 24px;">' . __('Your RRZE Service Catalogue', 'rrze-servicekatalog') . '</h1>', 0, 1);

        $pdf->SetY($pdf->getY() + 5);

        // Write Content
        $pdf->setEqualColumns(2, 80);

        foreach ($services as $id) {
            $meta = get_post_meta($id);

            // Title
            $pdf->SetTextColor(40, 60, 122);
            $pdf->WriteHTMLCell(0, 0, NULL, NULL, '<h2 style="font-size: 18px;">' . get_the_title($id) . '</h2>', 0, 1);

            // Description
            $pdf->SetY($pdf->getY() + 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFontSize(10);
            $pdf->WriteHTMLCell(0, 0, NULL, NULL, Utils::getMeta($meta, 'description'), 0, 1);

            // Commitment
            $commitmentTerms = get_the_terms($id, 'rrze-service-commitment');
            if ($commitmentTerms) {
                $commitmentName = $commitmentTerms[0]->name;
                $commitmentColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', TRUE);
                if ($commitmentColor == '') {
                    $commitmentColor = '#ffffff';
                }
                // hex2rgb
                (strlen($commitmentColor) === 4) ? list($commitmentColorR, $commitmentColorG, $commitmentColorB) = sscanf($commitmentColor, "#%1x%1x%1x") : list($commitmentColorR, $commitmentColorG, $commitmentColorB) = sscanf($commitmentColor, "#%2x%2x%2x");

                $pdf->SetY($pdf->getY() + $pdf->getFontSize() / 2, FALSE);
                $pdf->Rect($pdf->getX(), $pdf->getY() + 0.25, 2.5, 4, 'DF', ['all' => ['width' => 1, 'color' => ['255', '255', '255']]], [$commitmentColorR, $commitmentColorG, $commitmentColorB]);
                $pdf->MultiCell(0, 0, __('Use', 'rrze-servicekatalog') . ': ' . $commitmentName, 0, 'L', FALSE, 1, $pdf->getX() + 2, NULL);
                $pdf->SetY($pdf->GetY() + 2);
            }

            // QR Code Service Description
            $nextY = $pdf->getY();
            $urlDescription = esc_url(Utils::getMeta($meta, 'url-description'));
            if ( ! empty($urlDescription)) {
                $style = [
                    'border' => FALSE,
                    'hpadding' => 1,
                    'bgcolor' => FALSE
                ];
                $nextY = $pdf->getY() + 30;
                $pdf->write2DBarcode(Utils::getMeta($meta, 'url-description'), 'QRCODE,H', NULL, NULL, 22, 20, $style, 'T');
            }

            // URL Service Description
            if ( ! empty($urlDescription)) {
                $pdf->WriteHTMLCell(0, 5, NULL, NULL, '<a href="' . $urlDescription . '" style="color: #004a9f;">' . $urlDescription . '</a>', 0, 1);
            }

            $pdf->SetY($nextY);
        }

        $pdf->ResetColumns();

        // Close and output PDF document
        $pdf->Output('servicekatalog_' . date('Y-m-d') . '.pdf', 'I');
        exit;
    }
}

class RRZE_PDF extends TCPDF{
    //Page header
    public function Header() {
        $logoFAU = plugin()->getPath() . 'assets/img/logo_fau.svg';
        $this->SetX(12.5);
        $this->ImageSVG($logoFAU, null, 5, 50, 30, '', 'N', '');
        $this->setXY(130, 11);
        $this->SetFont('helvetica', '', 10, '', true);
        $this->setFontSpacing(0.1);
        $this->SetTextColor(40,60,122);
        $this->WriteHTMLCell(65, 30, null, null, 'Friedrich-Alexander-Universität<br />Regionales Rechenzentrum Erlangen', 0, 1);
        $this->setFontSpacing(0);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        $this->SetTextColor(40,60,122);
        $this->SetFontSize(8);
        $this->MultiCell(110, 18, "Regionales Rechenzentrum Erlangen (RRZE) | Martensstraße 1 | 91058 Erlangen \nStand: " . date('M Y'), 0, 'L', false, 0, null, null, true, 0, false, true, 18, 'B');
        //$this->Cell(110, 5, "Regionales Rechenzentrum Erlangen (RRZE) | Martensstraße 1 | 91058 Erlangen", 0, 1, 'L', false, '', 0, true, 'T', 'B');
        //$this->Cell(110, 5, "Stand: " . date('F Y'), 0, 1, 'L', false, '', 0, true, 'T', 'B');
        $this->SetX(155);
        // $this->SetY(268, false);
        $logoRRZE = plugin()->getPath() . 'assets/img/logo_rrze.svg';
        $this->ImageSVG($logoRRZE, '', '', 40, 18, '', 'T', 'R');
    }
}
