<?php
namespace Think;
/**
 * Excel 操作类
 */
class CExcel
{
	public function export($data,$fields,$excelFileName,$sheetTitle){
		Vendor("PHPExcel");
		Vendor("PHPExcel.Writer.Excel5");
		Vendor("PHPExcel.Writer.Excel2007");

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle($sheetTitle);

		//标题
		$j = 'A';
		for($m=0;$m<count($fields);$m++){
			$objActSheet->setCellValue($j.intval(1),$fields[$m]);
			$j++;
		}

		//内容
		$i = 2;
		foreach($data as $key=>$value){
			$j = 'A';
			foreach($value as $value2){
				$objActSheet->setCellValue($j.$i,$value2);
				$j++;
			}
			$i++;
		}

		//最后一行加 coobar 标识
		$objActSheet->setCellValue("A".$i,"coobar");

		ob_end_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition:attachment;filename="'.$excelFileName.'.xls"');
		header("Content-Transfer-Encoding:binary");

		$objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
	}

	public function selectExcel($filePath){
		Vendor("PHPExcel");
		Vendor("PHPExcel.Writer.Excel5");
		Vendor("PHPExcel.Writer.Excel2007");

		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				return array();
			}
		}
		$PHPExcel = $PHPReader->load($filePath);
		$currentSheet = $PHPExcel->getSheet(0);
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
		$ImportData = array();
		for($currentRow = 1;$currentRow <= $allRow;$currentRow++){
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
				if ($val instanceof PHPExcel_RichText){
					$val = $val->getPlainText();
				}
				$ImportData[$currentRow][] = $val;
			}
		}
		return $ImportData;
	}
}

?>