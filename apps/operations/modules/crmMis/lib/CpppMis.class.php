<?php

// Neha Gupta
// This class handles all the logics related to CPPP MIS.

class CpppMis
{
	private $start_dt;
	private $end_dt;

	public function __construct($start_dt, $end_dt)
	{
		$this->start_dt = $start_dt." 00:00:00";
		$this->end_dt = $end_dt." 23:59:59";
	}

	public function fetchDataForMIS()
	{
		$jprofileObj = new JPROFILE('newjs_slave');
		$srcWiseProfileArr = $jprofileObj->fetchSourceWiseProfiles($this->start_dt, $this->end_dt);
		unset($jprofileObj);

		foreach ($srcWiseProfileArr as $src => $profileArr) 
		{
			$srcObj = new MIS_SOURCE('newjs_slave');
			$srcGrp = $srcObj->getSourceGroup($src);
			unset($srcObj);

			if(!$src || !$srcGrp)
				$srcGrp = 'EMPTY';
			else
				$srcGrp = $srcGrp['GROUPNAME'];

			$srcWiseDataArr[$srcGrp]['REG'] += count($profileArr);

			$purchasesObj = new BILLING_PURCHASES('newjs_slave');
			$profileStr = implode(',', $profileArr);
			$profileArr = $purchasesObj->isPaidEver($profileStr, $this->start_dt);

			foreach($profileArr as $pid)
			{
				$data = $purchasesObj->fetchAmountPaid($pid);
				if($data && $data['AMT'] > 0)
				{
					$srcWiseDataArr[$srcGrp]['PAID_MEM']++;
					$srcWiseDataArr[$srcGrp]['TRANS'] += $data['CNT'];
					$srcWiseDataArr[$srcGrp]['AMT_PAID'] += $data['AMT'];
				}
			}
		}

		unset($srcWiseProfileArr);
		$totalArr = array('REG' => 0, 'PAID_MEM' => 0, 'TRANS' => 0, 'AMT_PAID' => 0);

		$misObj = new misGenerationhandler();

		foreach($srcWiseDataArr as $srcGrp => $data)
		{
			$srcWiseDataArr[$srcGrp]['AMT_PAID'] = $misObj->net_off_tax_calculation($srcWiseDataArr[$srcGrp]['AMT_PAID'], $this->end_dt);

			if($srcWiseDataArr[$srcGrp]['TRANS'] > 0)
				$srcWiseDataArr[$srcGrp]['AVG_AMT_PAID'] = round($srcWiseDataArr[$srcGrp]['AMT_PAID']/$srcWiseDataArr[$srcGrp]['TRANS']);

			$totalArr['REG'] += $srcWiseDataArr[$srcGrp]['REG'];
			$totalArr['PAID_MEM'] += $srcWiseDataArr[$srcGrp]['PAID_MEM'];
			$totalArr['TRANS'] += $srcWiseDataArr[$srcGrp]['TRANS'];
			$totalArr['AMT_PAID'] += $srcWiseDataArr[$srcGrp]['AMT_PAID'];

			if($totalArr['TRANS'] > 0)
				$totalArr['AVG_AMT_PAID'] = round($totalArr['AMT_PAID']/$totalArr['TRANS']);
		}
		return array($srcWiseDataArr, $totalArr);
	}

	public function createExcelFormatOutput($srcWiseDataArr, $totalArr, $header, $displayDate)
	{
		$header .= "\n\nSource\tNo_of_Registrations\tNo_of_Paid_Members\tAverage_Amount_Paid(net_of_tax)\n";

		foreach($srcWiseDataArr as $srcGrp => $data)
		{
			$message .= $srcGrp."\t";
			if($data['REG'])
				$message .= $data['REG']."\t";
			if($data['PAID_MEM'])
				$message .= $data['PAID_MEM']."\t";
			if($data['AVG_AMT_PAID'])
				$message .= $data['AVG_AMT_PAID']."\t";
			$message .= "\n";
		} 
		$message .= "GRAND TOTAL\t";
		if($totalArr['REG'])
			$message .= $totalArr['REG']."\t";
		if($totalArr['PAID_MEM'])
			$message .= $totalArr['PAID_MEM']."\t";
		if($totalArr['AVG_AMT_PAID'])
			$message .= $totalArr['AVG_AMT_PAID']."\t";

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=CPPP_MIS_".$displayDate.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $header."\n".$message;
		die;
	}
}
?>