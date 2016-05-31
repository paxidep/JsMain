<?php
class NEWJS_JP_NTIMES extends TABLE{
       

        /**
         * @fn __construct
         * @brief Constructor function
         * @param $dbName - Database to which the connection would be made
         */

        public function __construct($dbname="newjs_masterRep")
        {
			parent::__construct($dbname);
        }
        public function getProfileViews($pid)
        {
			try 
			{
				if($pid)
				{ 
					$sql="SELECT NTIMES FROM newjs.JP_NTIMES WHERE PROFILEID=:PROFILEID";
					$prep=$this->db->prepare($sql);
					$prep->bindValue(":PROFILEID",$pid,PDO::PARAM_INT);
					$prep->execute();
					if($result = $prep->fetch(PDO::FETCH_ASSOC))
					{
						return $result["NTIMES"];
					}
				}	
			}
			catch(PDOException $e)
			{
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
			}
		}
                
        public function updateProfileViews($pid)
        {
			try 
			{
				if($pid)
				{ 
					$sql="UPDATE newjs.JP_NTIMES SET NTIMES = NTIMES+1 WHERE PROFILEID=:PROFILEID";
					$prep=$this->db->prepare($sql);
					$prep->bindValue(":PROFILEID",$pid,PDO::PARAM_INT);
					$prep->execute();
					if($prep->rowCount() <= 0)
					{   
                                            $sql2="INSERT IGNORE INTO newjs.JP_NTIMES(PROFILEID,NTIMES) VALUES(:PROFILEID,1)";
                                            $prep2=$this->db->prepare($sql2);
                                            $prep2->bindValue(":PROFILEID",$pid,PDO::PARAM_INT);
                                            $prep2->execute();
					}
				}	
			}
			catch(PDOException $e)
			{
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
			}
		}
		
		
}
?>