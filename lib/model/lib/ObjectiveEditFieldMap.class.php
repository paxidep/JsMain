<?php
class ObjectiveEditFieldMap
{
	public static function getFieldMapKey($profileField,$from_whr="")
	{
			$objectiveFieldMap=array(
				'COUNTRY_RES'=>'country',
				'CITY_RES'=>'city',
				'EDU_LEVEL'=>'education_label',
				'RELATION'=>'relation',
				'COUNTRY_BIRTH'=>'country',
				'RES_STATUS'=>'residency_status',
				'BTYPE'=>'bodytype',
				'INCOME'=>'income_level',
				'FAMILY_BACK'=>'family_background',
				'EDU_LEVEL_NEW'=>'education',
				'MARRIED_WORKING'=>'working_marriage',
				'MOTHER_OCC'=>'mother_occupation',
				'LIVE_WITH_PARENTS'=>'live_with_parents',
				'FAMILY_INCOME'=>'income_level',
				'MTONGUE' =>'community',
				'SHOWMESSENGER'=>'privacy_option',
				'SHOWADDRESS'=>'privacy_option',
				'HAVECHILD'=>'children',
				'P_LRS'=>'lincome',
				'P_HRS'=>'hincome',
				'P_LDS'=>'lincome_dol',
				'P_HDS'=>'hincome_dol',
				'T_BROTHER'=>'sibling',
				'T_SISTER'=>'sibling',
				'M_BROTHER'=>'sibling',
				'M_SISTER'=>'sibling',
				'PARENT_CITY_SAME'=>'live_with_parents',
				'SHOWPHONE'=>'privacy_option',
				'SHOWMOBILE'=>'privacy_option',
				'HIV'=>'hiv_edit',
				'P_COUNTRY'=>'country',
				'P_CITY'=>'city',
				'P_CASTE'=>'caste',
				'P_MSTATUS'=>'marital_status',
				'P_RELIGION'=>'religion',
				'P_MTONGUE'=>'community',
				'P_MANGLIK'=>'manglik',
				'P_DIET'=>'diet',
				'P_SMOKE'=>'smoke',
				'P_DRINK'=>'drink',
				'P_COMPLEXION'=>'complexion',
				'P_BTYPE'=>'bodytype',
				'P_CHALLENGED'=>'handicapped',
				'P_NCHALLENGED'=>'nature_handicap',
				'P_EDUCATION'=>'education',
				'P_OCCUPATION'=>'occupation',
        'P_HAVECHILD'=>'children',
        'NATIVE_COUNTRY'=>'country',
				'NATIVE_CITY'=>'city_india',
				'NATIVE_STATE'=>'state_india',
        'MOBILE_NUMBER_OWNER'=>'number_owner',
        'PHONE_NUMBER_OWNER'=>'number_owner',
        'ALT_MOBILE_NUMBER_OWNER'=>'number_owner',
        'SHOWALT_MOBILE'=>'privacy_option',
        'SHOWPHONE_MOB'=>'privacy_option',
        'SHOWPHONE_RES'=>'privacy_option',
        'SHOW_PARENTS_CONTACT'=>'privacy_option',
        'ID_PROOF_TYP'=>'id_proof_typ',
        'P_HHEIGHT'=>'height',
        'P_LHEIGHT'=>'height',
        'P_STATE'=>'state_india',
        'P_OCCUPATION_GROUPING'=>'occupation_grouping',
			);
		if($from_whr=="MR")
			$objectiveFieldMap["RELATIONSHIP"]="relationship_minireg";
		if(array_key_exists($profileField,$objectiveFieldMap))
			return $objectiveFieldMap[$profileField];
		else
			return strtolower($profileField);
	}
}
