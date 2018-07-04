<?php
	function rollAttack($intModifier, $blnAdvantage = false)
	{
		$intRoll = rand(1, 10) + $intModifier;
		if ($blnAdvantage)
		{
			$intAdvantage = rand(1, 10) + $intModifier;
			if ($intAdvantage > $intRoll)
			{
				$intRoll = $intAdvantage;
			}
		}
		return $intRoll;
	}

	function attack(&$objAttacker, &$objDefender)
	{
		$arrDeaths = array(false, false);
		$intAttackModifier = $objAttacker->objOwner->arrStats[$objAttacker->strAttackerStat];
		$intDefendModifier = $objDefender->objOwner->arrStats[$objAttacker->strDefenderStat];
		
		$intAttackRoll = rollAttack($intAttackModifier, $objAttacker->objOwner->hasAttackAdvantage($objAttacker->strAttackerStat));
		$intDefendRoll = rollAttack($intDefendModifier, $objDefender->objOwner->hasDefendAdvantage($objAttacker->strAttackerStat));
		
//		$intAttackRoll = 10;
//		$intDefendRoll = 5;

		$intBaseAttack = $intAttackRoll - $intAttackModifier;
		$intBaseDefense = $intDefendRoll - $intDefendModifier;
		if ($objAttacker->objOwner->blnBookAdv && $intAttackRoll < $intDefendRoll)
		{
			$objAttacker->objOwner->blnBookAdv = false;
			// Determine which roll was more extreme and then re-roll it
			if ((10 - $intBaseAttack) >= ($intBaseDefense - 1))
			{
				$intAttackRoll = rollAttack($intAttackModifier, false);
				$intBaseAttack = $intAttackRoll - $intAttackModifier;
			}
			else
			{
				$intDefendRoll = rollAttack($intDefendModifier, false);
				$intBaseDefense = $intDefendRoll - $intDefendModifier;
			}
		}
		if ($objDefender->objOwner->blnBookAdv && $intAttackRoll > $intDefendRoll)
		{
			$objDefender->objOwner->blnBookAdv = false;
			// Determine which roll was more extreme and then re-roll it
			if ((10 - $intBaseDefense) >= ($intBaseAttack - 1))
			{
				$intDefendRoll = rollAttack($intDefendModifier, false);
				$intBaseDefense = $intDefendRoll - $intDefendModifier;
			}
			else
			{
				$intAttackRoll = rollAttack($intAttackModifier, false);
				$intBaseAttack = $intAttackRoll - $intAttackModifier;
			}
		}
		if ($intAttackRoll > $intDefendRoll)
		{
			$intDamage = $objAttacker->attackDamage();
			
			$objDefender->intHp -= $intDamage;

			if ($objDefender->intHp <= 0)
			{
				$objDefender->intHp = 0;
			}
		}
		elseif ($intDefendRoll > $intAttackRoll)
		{
			$intDamage = $objDefender->counterDamage();
			$objAttacker->intHp -= $intDamage;
			if ($objAttacker->intHp <= 0)
			{
				$objAttacker->intHp = 0;
			}
		}
		else
		{
			$intDamage = $objAttacker->attackDamage();
			$objDefender->intHp -= $intDamage;
			if ($objDefender->intHp <= 0)
			{
				$objDefender->intHp = 0;
			}
			
			$intDamage = $objDefender->counterDamage();
			$objAttacker->intHp -= $intDamage;
			if ($objAttacker->intHp <= 0)
			{
				$objAttacker->intHp = 0;
			}
		}
	}
	
	function resolveAttack(&$objAttackerAsset, $strTarget, &$arrFactions)
	{
		$objDefendingRule = new DefendRule(NULL, NULL, NULL, NULL, 0);
		foreach ($arrFactions as $objDefender)
		{
			foreach ($objDefender->arrRules as $objRule)
			{
				if ($objRule->isMet($strTarget) && $objRule->intInitiative > $objDefendingRule->intInitiative)
				{
					$objDefendingRule = $objRule;
				}
			}
		}
		if (is_null($objDefendingRule->strTargetFaction) || count($objDefender->arrAssets) == 0)
		{
			// No valid rule found, assume everything is dead
			return;
		}
		attack($objAttackerAsset, $objDefendingRule->objDefendingAsset);
	}
?>