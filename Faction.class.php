<?php
require_once 'Asset.class.php';
require_once 'DefendRule.class.php';
require_once 'WatchRule.class.php';

class Faction
{
	public $strName;
	public $arrStats;
	public $intOrder;
	public $blnHasAdv = false;
	public $strAdvStat;
	public $intAdvDir;
	public $blnBookAdv = false;
	public $arrAssets;
	public $arrWatches;
	public $arrRules;
	
	public function __construct($strName, $arrStats, $intOrder, $strAdvStat = NULL, $intAdvDir = 0)
	{
		$this->strName = $strName;
		$this->arrStats = $arrStats;
		$this->intOrder = $intOrder;
		if (!isset($this->arrStats['F']) || !isset($this->arrStats['C']) || !isset($this->arrStats['W']))
		{
			die("The faction " . $strName . " did not have its stats set correctly upon creation.");
		}

		$this->strAdvStat = $strAdvStat;
		$this->intAdvDir = $intAdvDir;
		if (!is_null($this->strAdvStat))
		{
			$this->blnHasAdv = true;
		}
		$this->arrAssets = array();
		$this->arrRules = array();
		$this->arrWatches = array();
	}
	
	public function addAsset($strName, $strWatchCondition = "", $intHpOverride = NULL)
	{
		$objAsset = new Asset($this, $strName, $intHpOverride);
		$this->arrAssets[] = $objAsset;
		
		if ($strWatchCondition !== "")
		{
			$this->arrWatches[] = array(new WatchRule($objAsset, $strWatchCondition), 0);
		}
	}
	
	public function addRule($strTargetFaction, $mixAsset, $strCondition, $intInitiative)
	{
		if (gettype($mixAsset) == "string")
		{
			foreach ($this->arrAssets as $objAsset)
			{
				if ($objAsset->strName == $mixAsset)
				{
					$objRule = new DefendRule($strTargetFaction, $objAsset, $strCondition, $intInitiative);
					$this->arrRules[] = $objRule;
				}
			}
		}
		else
		{
			$objRule = new DefendRule($strTargetFaction, $objAsset, $strCondition, $intInitiative);
			$this->arrRules[] = $objRule;
		}
	}

	public function hasAttackAdvantage($strAttackStat)
	{
		if ($this->strAdvStat == $strAttackStat && $this->intAdvDir == 1 && $this->blnHasAdv)
		{
			$this->blnHasAdv = false;
			return true;
		}
		return false;
	}
	
	public function hasDefendAdvantage($strAttackStat)
	{
		if ($this->strAdvStat == $strAttackStat && $this->intAdvDir == -1 && $this->blnHasAdv)
		{
			$this->blnHasAdv = false;
			return true;
		}
		return false;
	}
	
	public function checkRules()
	{
		$objActiveRule = NULL;
		foreach ($this->arrRules as $objRule)
		{
			if ($objRule->isMet())
			{
				if (is_null($objActiveRule) || $objRule->intInitiative > $objActiveRule->intInitiative)
				{
					$objActiveRule = $objRule;
				}
			}
		}
		return $objActiveRule;
	}
	
	public function checkWatches()
	{
		foreach ($this->arrWatches as $key => $arrWatch)
		{
			if ($arrWatch[0]->isMet())
			{
				$this->arrWatches[$key][1]++;
			}
		}
	}
	
	public function reportWatches($intIterations)
	{
		echo $this->strName . ":\n";
		foreach ($this->arrWatches as $arrWatch)
		{
			$fltRate = round((float)$arrWatch[1] / (float)($intIterations / 100), 3);
			echo "\t" . $arrWatch[0]->objAsset->strName . " " . $arrWatch[0]->strCondition . ": " . $fltRate . "%\n\n";
		}
	}
	
	public function reset()
	{
		if (!is_null($this->strAdvStat))
		{
			$this->blnHasAdv = true;
		}
		foreach ($this->arrAssets as $objAsset)
		{
			$objAsset->reset();
		}
	}

	//This is currently unused, would only be useful for sorting factions from a list to determine turn order
	public static function compareOrder($objFactionA, $objFactionB)
	{
		if ($objFactionA->intOrder > $objFactionB->intOrder)
		{
			return 1;
		}
		elseif ($objFactionB->intOrder > $objFactionA->intOrder)
		{
			return -1;
		}
		return 0;
	}
}
?>
