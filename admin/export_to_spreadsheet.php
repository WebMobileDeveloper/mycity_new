<?php
include_once 'includes/functions.php'; 
/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("mycity.com")
							 ->setLastModifiedBy("mycity.com")
							 ->setTitle("MyCity Document")
							 ->setSubject("MyCity User Profiles")
							 ->setDescription("mycity.com users")
							 ->setKeywords("user mycity")
							 ->setCategory("mycity file");

$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', ' ')
					->setCellValue('B1', ' ')
					->setCellValue('C1', 'Shared')
					->setCellValue('D1', 'City Info w Zip')
					->setCellValue('E1', 'Phone')
					->setCellValue('F1', 'Vocation')
					->setCellValue('G1', 'Targeted clients')
					->setCellValue('H1', 'Targeted Partners')
					->setCellValue('I1', 'About Yourself');
					
if ($stmt = $link->prepare("SELECT id, username FROM mc_user ORDER BY id")) {



	/* execute query */
	$stmt->execute();

	/* bind result variables */
	$stmt->bind_result($id, $username);

	$jobs = array();
	/* fetch value */
	$counter = 2;
	while ($stmt->fetch()) {
		$url = 'http://www.mycity.com/profile/?l=' . $id;
		
		if (!empty($username)) {
			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A' . $counter, $username)
						->setCellValue('B' . $counter, $url);
			$objPHPExcel->setActiveSheetIndex(0)
						->getCell('B' . $counter)
						->getHyperlink()
						->setUrl($url);
				
				$objPHPExcel->setActiveSheetIndex(0)
						->getStyle('B' . $counter)
						->applyFromArray(array( 'font' => array( 'color' => ['rgb' => '0000FF'], 'underline' => 'single' ) ));
			
			/*
			$data[] = array(
				'id' => $id, 'username' => $username
			);*/
			$counter++;
		}
	}

	/* close statement */
	$stmt->close();
}






			
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('MyCity User Profiles');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="MyCity User Profile.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;