<?php
class billing_VARIABLE_DISCOUNT_POOL_TECH extends TABLE{
       
        public function __construct($dbname="")
        {
			parent::__construct($dbname);
        }
	public function fetchVdPoolTechProfiles($filterVDType="")
	{
		try
		{
                        $joinCondition = "";
                        if(!empty($filterVDType)){
                                $joinCondition = "billing.VARIABLE_DISCOUNT_POOL_TECH.PROFILEID=billing.VARIABLE_DISCOUNT.PROFILEID AND billing.VARIABLE_DISCOUNT.TYPE NOT IN(".$filterVDType.")";
                        }
                        else{
                                $joinCondition = "billing.VARIABLE_DISCOUNT_POOL_TECH.PROFILEID=billing.VARIABLE_DISCOUNT.PROFILEID";
                        }
			$sql = "SELECT VARIABLE_DISCOUNT_POOL_TECH.PROFILEID FROM billing.VARIABLE_DISCOUNT_POOL_TECH left join billing.VARIABLE_DISCOUNT on (".$joinCondition.") WHERE billing.VARIABLE_DISCOUNT.PROFILEID IS NULL";
			$res = $this->db->prepare($sql);
                        $res->execute();
			while($row = $res->fetch(PDO::FETCH_ASSOC))
			{
				$profilesArr[] =$row['PROFILEID'];
			}
		}
		catch(Exception $e)
                {
                        throw new jsException($e);
                }
		return $profilesArr;
	}
        public function getProfilesForScore($profileStr,$score)
        {
                try
                {
                        $sql = "SELECT PROFILEID FROM billing.VARIABLE_DISCOUNT_POOL_TECH WHERE PROFILEID IN($profileStr) AND SCORE<=:SCORE";
                        $res = $this->db->prepare($sql);
			$res->bindValue(":SCORE", $score, PDO::PARAM_INT);
                        $res->execute();
                        while($row = $res->fetch(PDO::FETCH_ASSOC))
                        {
                                $profilesArr[] =$row['PROFILEID'];
                        }
                }
                catch(Exception $e)
                {
                        throw new jsException($e);
                }
                return $profilesArr;
        }
}
?>
