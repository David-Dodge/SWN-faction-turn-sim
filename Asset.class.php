<?php
require_once("DefendRule.class.php");

class Asset
{
	public $objOwner;
	public $strName;
	public $strAttackerStat = NULL;
	public $strDefenderStat = NULL;
	public $intHp;
	
	private $strAttackRoll;
	private $strDefendRoll;

	public $arrStats;

	public function __construct($objOwner, $strName, $intHpOverride = NULL)
	{
		$this->objOwner = $objOwner;

		$strFile = $strName . ".asset";
		$arrDetails = explode(",", file_get_contents("assets/" . $strFile));
		$this->strName = $strName;
		$this->intHp = intval($arrDetails[0]);
		if (!is_null($intHpOverride))
		{
			$this->intHp = $intHpOverride;
		}
		$this->intMaxHp = $this->intHp;
		if ($arrDetails[2] != "")
		{
			$this->strAttackerStat = substr($arrDetails[2], 0, 1);
			$this->strDefenderStat = substr($arrDetails[2], 2, 1);
		}
		$this->strAttackRoll = $arrDetails[3];
		$this->strDefendRoll = $arrDetails[4];
		
	}
	
	public function attackDamage()
	{
		return $this->calculateDamage($this->strAttackRoll);
	}

	private function calculateDamage($strRollString)
	{
		if (strlen($strRollString) == 0)
		{
			return NULL;
		}
		$intTotalDice = intval(substr($strRollString, 0, 1));
		$intDiceSize = intval(substr($strRollString, 2));
		$intDiceAdded = substr($strRollString, 4, 1);
		if ($intDiceAdded === FALSE)
		{
			$intDiceAdded = 0;
		}
		else
		{
			$intDiceAdded = intval($intDiceAdded);
		}
		
		$intDamage = 0;
		for ($i = 0; $i < $intTotalDice; $i++)
		{
			$intDamage += rand(1, $intDiceSize);
		}
		$intDamage += $intDiceAdded;

		return $intDamage;
	}
	
	public function counterDamage()
	{
		return $this->calculateDamage($this->strDefendRoll);
	}
	
	public function reset()
	{
		$this->intHp = $this->intMaxHp;
	}
}
?>
