<?php
require_once 'Rule.class.php';

class DefendRule
{
	public $strTargetFaction;
	public $objDefendingAsset;
	public $strDefendCondition;
	public $intInitiative;
	
	public function __construct($strTargetFaction, $objDefendingAsset, $strDefendCondition, $intInitiative)
	{
		$this->strTargetFaction = $strTargetFaction;
		$this->objDefendingAsset = $objDefendingAsset;
		$this->strDefendCondition = $strDefendCondition;
		$this->intInitiative = $intInitiative;
	}
	
	public function isMet($strTargetFaction)
	{
		if ($strTargetFaction != $this->strTargetFaction)
		{
			return false;
		}
		$arrConditionPieces = explode(" ", $this->strDefendCondition);
		if ($arrConditionPieces[0] == "HP")
		{
			switch ($arrConditionPieces[1])
			{
				case ">":
					return $this->objDefendingAsset->intHp > intval($arrConditionPieces[2]);
				case ">=":
					return $this->objDefendingAsset->intHp >= intval($arrConditionPieces[2]);
				case "=":
					return $this->objDefendingAsset->intHp == intval($arrConditionPieces[2]);
				case "<=":
					return $this->objDefendingAsset->intHp <= intval($arrConditionPieces[2]);
				case "<":
					return $this->objDefendingAsset->intHp < intval($arrConditionPieces[2]);
			}
			//If we here your rule was invalid
		}
	}
}
?>