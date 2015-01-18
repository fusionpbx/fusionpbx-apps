<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (if_group("admin") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//action invoice_uuid
	if (isset($_REQUEST["id"])) {
		$invoice_uuid = check_str($_REQUEST["id"]);
		$type = check_str($_REQUEST["type"]);
	}

//get the invoice details
	$sql = "select * from v_invoices ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and invoice_uuid = '$invoice_uuid' ";
	$sql .= "order by invoice_uuid desc ";
	$sql .= "limit 1 ";
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch();
		$invoice_number = $row['invoice_number'];
		$contact_uuid_from = $row['contact_uuid_from'];
		$contact_uuid_to = $row['contact_uuid_to'];
		$invoice_date = $row['invoice_date'];
		$invoice_note = $row['invoice_note'];
		unset ($prep_statement);
	}

//prepare the invoice date
	$invoice_date = date("d", strtotime($invoice_date)).' '.date("M", strtotime($invoice_date)).' '.date("Y", strtotime($invoice_date));

//prepare to use fpdf
	define('FPDF_FONTPATH',$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/resources/fpdf/font/');
	require('resources/fpdf/fpdf.php');

//create the fpdf object and add the first page
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',9);

//get contact from name
	$sql = "select * from v_contacts ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and contact_uuid = '$contact_uuid_from' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$from_contact_organization = $row["contact_organization"];
		$from_contact_name_given = $row["contact_name_given"];
		$from_contact_name_family = $row["contact_name_family"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//get contact from address
	$sql = "select * from v_contact_addresses ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and contact_uuid = '$contact_uuid_from' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$from_address_type = $row["address_type"];
		$from_address_street = $row["address_street"];
		$from_address_extended = $row["address_extended"];
		$from_address_locality = $row["address_locality"];
		$from_address_region = $row["address_region"];
		$from_address_postal_code = $row["address_postal_code"];
		$from_address_country = $row["address_country"];
		break; //limit to 1 row
	}
	unset ($prep_statement);
	$pdf->SetY(10);
	$pdf->SetFont('Arial','B',9);
	if (strlen($from_contact_organization) > 0) {
		$pdf->Cell(40,5,$from_contact_organization);
		$pdf->Ln();
	}
	else {
		if (strlen($from_contact_name_given.$from_contact_name_family) > 0) {
			$pdf->Cell(40,5,$from_contact_name_given.' '.$from_contact_name_family);
			$pdf->Ln();
		}
	}
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(40,5,$from_address_street.' '.$from_address_extended);
	$pdf->Ln();
	$pdf->Cell(40,5,$from_address_locality.', '.$from_address_region.' '.$from_address_country.' '.$from_address_postal_code);
	$pdf->Ln();
	$pdf->Ln();

//get contact to name
	$sql = "select * from v_contacts ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and contact_uuid = '$contact_uuid_to' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$to_contact_organization = $row["contact_organization"];
		$to_contact_name_given = $row["contact_name_given"];
		$to_contact_name_family = $row["contact_name_family"];
	}
	unset ($prep_statement);

//get contact to address
	$sql = "select * from v_contact_addresses ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and contact_uuid = '$contact_uuid_to' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$to_address_type = $row["address_type"];
		$to_address_street = $row["address_street"];
		$to_address_extended = $row["address_extended"];
		$to_address_locality = $row["address_locality"];
		$to_address_region = $row["address_region"];
		$to_address_postal_code = $row["address_postal_code"];
		$to_address_country = $row["address_country"];
		$to_address_description = $row["address_description"];
		break; //limit to 1 row
	}
	unset ($prep_statement);
	$pdf->SetY(40);
	$pdf->SetFont('Arial','B',9);
	if (strlen($to_contact_organization) > 0) {
		$pdf->Cell(40,5,$to_contact_organization);
		$pdf->Ln();
	}
	else {
		if (strlen($to_contact_name_given.$to_contact_name_family) > 0) {
			$pdf->Cell(40,5,$to_contact_name_given.' '.$to_contact_name_family);
			$pdf->Ln();
		}
	}
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(40,5,$to_address_street.' '.$to_address_extended);
	$pdf->Ln();
	if (strtolower($to_address_country) == 'portugal') {
		$pdf->Cell(40,5,$to_address_postal_code.' '.$to_address_locality.' '.$to_address_region.' '.$to_address_country);
	}
	else {
		$pdf->Cell(40,5,$to_address_locality.', '.$to_address_region.' '.$to_address_country.' '.$to_address_postal_code);
	}
	$pdf->Ln();
	$pdf->Cell(40,5,$to_address_description);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();

//invoice info
	$pdf->SetY(10);
	$pdf->Cell(150,10,'');
	$pdf->SetFont('Arial','',23);
	if ($type == "quote") {
		$pdf->Cell(40,10,$text['label-quote']);	
	}
	else {
		$pdf->Cell(40,10,$text['label-invoice']);
	}
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(150,5,'');
	$pdf->Cell(40,5,$text['label-invoice_date'].' '.$invoice_date);
	$pdf->Ln();
	$pdf->Cell(150,5,'');
	$pdf->Cell(40,5,$text['label-invoice_number'].' '.$invoice_number);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();

//set the vertical position
	$pdf->SetY(65);

//table headers array
	$header = array($text['label-item_qty'], $text['label-item_desc'], $text['label-item_unit_price'], $text['label-item_amount']);

//set the table header styles
	$pdf->SetFillColor(200,200,200);
	//$pdf->SetTextColor(255);
	$pdf->SetDrawColor(220,220,220);
	$pdf->SetLineWidth(0.3);

//set the table cell widths
	$w[0] = 20;
	$w[1] = 120;
	$w[2] = 25;
	$w[3] = 25;
	for($i=0;$i<count($header);$i++) {
		if ($header[$i] == $text['label-item_desc']) {
			//left align
			$pdf->Cell($w[$i],5,$header[$i],1,0,'L',true);
		}
		else {
			//center
			$pdf->Cell($w[$i],5,$header[$i],1,0,'C',true);
		}
	}
	$pdf->Ln();

//set the text and background color
	$pdf->SetFillColor(224,235,255);
	$pdf->SetTextColor(0);
	$pdf->SetFont('Arial','',9);

//itemized list
	$sql = "select * from v_invoice_items ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and invoice_uuid = '$invoice_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$fill = false;
	$total = 0;
	foreach ($result as &$row) {
		$item_qty = $row["item_qty"];
		$item_desc = $row["item_desc"];
		//$item_desc = str_replace("\n", "<br />", $item_desc);
		$item_desc = wordwrap($item_desc, 70, "\n");
		$item_unit_price = $row["item_unit_price"];
		$item_sub_total = $item_qty * $item_unit_price;

		$item_desc_array = explode ("\n", $item_desc);
		$x = 0;
		foreach ($item_desc_array as $line) {
			//quantity
				if ($x == 0) {
					$pdf->Cell($w[0],6,$item_qty,'LR',0,'C',$fill);
				}
				else {
					$pdf->Cell($w[0],6," ",'LR',0,'C',$fill);
				}
			//description
				$pdf->Cell($w[1],6,$line,'LR',0,'L',$fill);
			//unit price
				if ($x == 0) {
					$pdf->Cell($w[2],6,$item_unit_price,'LR',0,'R',$fill);
				}
				else {
					$pdf->Cell($w[2],6," ",'LR',0,'R',$fill);
				}
			//amount
				if ($x == 0) {
					$pdf->Cell($w[3],6,number_format($item_sub_total,2),'LR',0,'R',$fill);
				}
				else {
					$pdf->Cell($w[3],6," ",'LR',0,'R',$fill);
				}
			//line feed
				$pdf->Ln(6);
				$x++;
		}
		//line seperator
			//$pdf->Cell(($w[0]+$w[1]+$w[2]+$w[3]),0.3," ",'TBRL',1,'R',$fill);
		//alternate the fill
			if ($fill) {
				$fill = false;
			}
			else {
				$fill = true;
			}
		//sub total
			$total = $total + $item_sub_total;
	}
	unset ($prep_statement);
	//line seperator
	$pdf->Cell(($w[0]+$w[1]+$w[2]+$w[3]),0.1," ",'TBRL',1,'R',$fill);

//show the total
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell($w[0],6,'','',0,'L','');
	$pdf->Cell($w[1],6,'','',0,'L','');
	$pdf->Cell($w[2],6,'','',0,'R','');
	$pdf->Cell($w[3],6,$text['label-invoice_total'].' $'.number_format($total,2).' USD','',0,'R','');
	$pdf->Ln();

	if (strlen($invoice_note) > 0) {
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell($w[0],6,$text['label-invoice_notes'],'',0,'L',$fill);
		$pdf->Ln();
		$pdf->Cell($w[0],6,''.$invoice_note,'',0,'L',$fill);
		$pdf->Ln();
	}

//closing line
	//$pdf->Cell(array_sum($w),0,'','T');

//show the pdf
	$pdf->Output();

?>