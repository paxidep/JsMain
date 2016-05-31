<?php
/**
* This class with populate the ids for which we need to send the mailers.
*/
class matchalerts_MATCHALERTS_TO_BE_SENT extends TABLE
{
	public function __construct()
	{
		$dbname = "matchalerts_slave";
		parent::__construct($dbname);
	}

	/**
	* Empty The table
	*/
        public function truncateTable()
        {
                try
                {
                        $sql="TRUNCATE TABLE matchalerts.MATCHALERTS_TO_BE_SENT";
			$res = $this->db->prepare($sql);
                        $res->execute();
                }
                catch (PDOException $e)
                {
			//add mail/sms
                        throw new jsException($e);
                }
        }

	/**
	* Populate the table as per conditiob "conditionNew"
	* @param conditionNew
	*/
        public function populateTables($conditionNew)
        {
                try
                {
			$sql="INSERT IGNORE INTO matchalerts.MATCHALERTS_TO_BE_SENT(PROFILEID) SELECT jp.PROFILEID FROM newjs.JPROFILE as jp LEFT JOIN newjs.JPROFILE_CONTACT as jpc ON jpc.PROFILEID = jp.profileid WHERE ".$conditionNew;
			$res = $this->db->prepare($sql);
                        $res->execute();
                }
                catch (PDOException $e)
                {
			//add mail/sms
                        throw new jsException($e);
                }
        }


	/**
	* Fetch 
	* @param 
	*/
	public function fetch($totalScript="1",$currentScript="0",$limit="")
	{
		try
		{
			$result = NULL;
			$sql = "SELECT PROFILEID , HASTRENDS FROM matchalerts.MATCHALERTS_TO_BE_SENT WHERE PROFILEID%:TOTAL_SCRIPT=:SCRIPT AND IS_CALCULATED=:STATUS";
			if($limit)
                                $sql.= " limit 0,:LIMIT";
                        $prep = $this->db->prepare($sql);
                        $prep->bindValue(":TOTAL_SCRIPT",$totalScript,PDO::PARAM_INT);
                        $prep->bindValue(":SCRIPT",$currentScript,PDO::PARAM_INT);
                        $prep->bindValue(":STATUS",'N',PDO::PARAM_STR);
                        if($limit)
                                  $prep->bindValue(":LIMIT",$limit,PDO::PARAM_INT);
                        $prep->execute();
			while($row = $prep->fetch(PDO::FETCH_ASSOC))
			{
				$result[$row["PROFILEID"]] = $row["HASTRENDS"];
			}
			return $result;
		}
		catch (PDOException $e)
		{
			throw new jsException($e);
		}
	}

	/**
	* update
	* @param pid
	*/
	public function update($pid)
	{
		try
		{
			$result = NULL;
			$sql = "UPDATE matchalerts.MATCHALERTS_TO_BE_SENT SET IS_CALCULATED='Y' WHERE PROFILEID=:PID";
                        $prep = $this->db->prepare($sql);
                        $prep->bindValue(":PID",$pid,PDO::PARAM_INT);
                        $prep->execute();
		}
		catch (PDOException $e)
		{
			throw new jsException($e);
		}
	}
        /*
         * 
         * This function updates HASTRENDS column if user has data in trends table
         */
        public function updateTrends(){
                try
		{
			$sql = "UPDATE matchalerts.MATCHALERTS_TO_BE_SENT m , twowaymatch.TRENDS t SET HASTRENDS = '1' WHERE m.PROFILEID =t.PROFILEID and ((t.INITIATED + t.ACCEPTED)>20)";
                        $prep = $this->db->prepare($sql);
                        $prep->execute();
		}
		catch (PDOException $e)
		{
			throw new jsException($e);
		}
        }
        /*
         * This function reset HASTRENDS column to '0' if user switched to old match logic
         */
        public function resetTrendsIfOldLogicSet(){
                try
		{
			$sql = "UPDATE matchalerts.MATCHALERTS_TO_BE_SENT m , newjs.MATCH_LOGIC t SET HASTRENDS = '0' WHERE m.PROFILEID =t.PROFILEID AND LOGIC_STATUS = 'O' AND HASTRENDS='1'";
                        $prep = $this->db->prepare($sql);
                        $prep->execute();
		}
		catch (PDOException $e)
		{
			throw new jsException($e);
		}
        }
}