<?php

/**
 * Fonction onBoot, lancée automatiquement par le framework juste après la résolution
 * de l'url, mais avant d'appeler le couple controller/action
 * @param $request
 * @param $config
 * @return void
 */
function onBoot(&$request, &$config)
{
	$params = &$request->getParams();
}