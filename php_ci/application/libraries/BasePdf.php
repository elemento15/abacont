<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/tcpdf/tcpdf.php';

class BasePdf extends TCPDF { 
	protected $title = 'PDF TITLE';
	protected $subtitle = 'Extra Information for PDF';

    public function __construct() {
    	parent::__construct();

    	$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$this->SetHeaderMargin(PDF_MARGIN_HEADER + 8);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER + 8);

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
    	$this->Line($this->GetX(), $this->GetY(), 195, $this->GetY());
    }

    protected function formatCurrency($number) {
        return '$'.number_format($number, 2);
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
}

/* End of file BasePdf.php */