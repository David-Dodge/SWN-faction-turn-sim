<?php
	require_once 'Asset.class.php';
	require_once 'Faction.class.php';
	require_once 'CombatFunctions.php';

	$intIterations = 500000;
	$arrFactions = array();
	$strAttacker = "HousesMinor";
	$strDefender = "Vagrant";

	// For all intensive purposes, the initiatives set to Factions don't matter unless you intend to
	// try to simulate multiple factions attacking each other in the same run
	$objFaction = new Faction("Vagrant", array("F" => 5, "C" => 6, "W" => 3), 1, "C", 1);
	$objFaction->addAsset("Demagogue", "HP <= 0");
	$objFaction->addAsset("Demagogue", "HP <= 0");
	$objFaction->addAsset("PsychicAssassins", "HP <= 0");
	$objFaction->addRule("Vagrant", "Demagogue", "HP >= 10", 10);
	$objFaction->addRule("Vagrant", "Demagogue", "HP >= 0", 9);
	$objFaction->addRule("Vagrant", "PsychicAssassins", "HP > 0", 8);
	$arrFactions["Vagrant"] = $objFaction;

	$objFaction = new Faction("UPC", array("F" => 5, "C" => 6, "W" => 3), 1);
	$objFaction->addAsset("Demagogue");
	$objFaction->addAsset("PsychicAssassins");
	$objFaction->addAsset("Seditionists");
	$arrFactions["UPC"] = $objFaction;

	$objFaction = new Faction("HousesMinor", array("F" => 7, "C" => 7, "W" => 7), 1, "C", 1);
	$objFaction->addAsset("CommoditiesBroker", "HP > 0");
	$objFaction->addAsset("PopularMovement", "HP > 0");
	$objFaction->addAsset("PopularMovement", "HP > 0");
	$objFaction->addAsset("TransitWeb", "HP > 0");
	$objFaction->blnBookAdv = true;
	$arrFactions["HousesMinor"] = $objFaction;

	for ($i = 0; $i < $intIterations; $i++)
	{
		foreach ($arrFactions[$strAttacker]->arrAssets as $intIndex => $objAsset)
		{
			resolveAttack($objAsset, $strDefender, $arrFactions);
		}
		
		$arrFactions["HousesMinor"]->blnBookAdv = true;
		foreach ($arrFactions as $objFaction)
		{
			$objFaction->checkWatches();
			$objFaction->reset();
		}
	}
	foreach ($arrFactions as $objFaction)
	{
		$objFaction->reportWatches($intIterations);
	}
?>
