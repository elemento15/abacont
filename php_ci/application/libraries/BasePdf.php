<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/tcpdf/tcpdf.php';

class BasePdf extends TCPDF { 
	protected $title = 'PDF TITLE';
	protected $subtitle = 'Extra Information for PDF';
    protected $orientation = 'P'; // (P) Portrait or (L) Landscape

    public function __construct() {
    	parent::__construct();

        // define as protected this attributes
        $margin_header = (isset($this->margin_header)) ? $this->margin_header : 8;
        $margin_footer = (isset($this->margin_footer)) ? $this->margin_footer : 8;

    	$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$this->SetHeaderMargin(PDF_MARGIN_HEADER + $margin_header);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER + $margin_footer);

		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Helvetica', '', 12);
    }

    // header method can be overloaded
    public function header() {
		$border = false;
    	$this->SetFont('Helvetica', 'B', 14);
    	$this->Cell(0, 0, $this->title, $border, 1, 'C', false);
    	$this->SetFont('Helvetica', 'B', 12);
    	$this->Cell(0, 0, $this->subtitle, $border, 1, 'C', false);
    	$this->Ln(1);

        if (method_exists($this, 'subHeader')) {
            $this->subHeader(); // define as protected
        }

        $line_size = ($this->orientation == 'P') ? 195 : 282;
    	$this->Line($this->GetX(), $this->GetY(), $line_size, $this->GetY());
    }

    protected function formatCurrency($number, $show_zero = true) {
        if (! $show_zero && $number == 0) {
            return '-';
        } else {
            return '$'.number_format($number, 2);
        }
    }

    protected function formatNumber($number, $show_zero = true) {
        if (! $show_zero && $number == 0) {
            return '-';
        } else {
            return number_format($number, 2);
        }
    }

    protected function setColorNegative($number) {
        if ($number < 0) {
            $this->SetTextColor(255, 0, 0);
        } else {
            $this->SetTextColor(0, 0, 0);
        }
    }

    protected function setColorDefault() {
        $this->SetTextColor(0, 0, 0);
    }

    protected function validDate($date) {
        if ( !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date )) { 
            return false;
        } else {
            return true;
        }
    }

    protected function getMonthName($month, $long_name = false) {
        $txt = '';

        switch (intval($month)) {
            case  1 : $txt = 'Enero';      break;
            case  2 : $txt = 'Febrero';    break;
            case  3 : $txt = 'Marzo';      break;
            case  4 : $txt = 'Abril';      break;
            case  5 : $txt = 'Mayo';       break;
            case  6 : $txt = 'Junio';      break;
            case  7 : $txt = 'Julio';      break;
            case  8 : $txt = 'Agosto';     break;
            case  9 : $txt = 'Septiembre'; break;
            case 10 : $txt = 'Octubre';    break;
            case 11 : $txt = 'Noviembre';  break;
            case 12 : $txt = 'Diciembre';  break;
        }

        return ($long_name) ? $txt : substr($txt, 0, 3);
    }
}

/* End of file BasePdf.php */