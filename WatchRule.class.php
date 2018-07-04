<?php
require_once 'Rule.class.php';

class WatchRule
{
	public $objOwningFaction;
	public $objAsset;
	
	public function __construct($objAsset, $strCondition)
	{
		$this->objAsset = $objAsset;
		$this->strCondition = $strCondition;
	}
	
	public function isMet()
	{
		$arrConditionPieces = explode(" ", $this->strCondition);
		if ($arrConditionPieces[0] == "HP")
		{
			switch ($arrConditionPieces[1])
			{
				case ">":
					return $this->objAsset->intHp > intval($arrConditionPieces[2]);
				case ">=":
					return $this->objAsset->intHp >= intval($arrConditionPieces[2]);
				case "=":
					return $this->objAsset->intHp == intval($arrConditionPieces[2]);
				case "<=":
					return $this->objAsset->intHp <= intval($arrConditionPieces[2]);
				case "<":
					return $this->objAsset->intHp < intval($arrConditionPieces[2]);
			}
			//If we here your rule was invalid
		}
	}
}
?>
