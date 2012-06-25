<?php

/**
 * Fonction onBoot, lancée automatiquement par le framework juste après la résolution
 * de l'url, mais avant d'appeler le couple controller/action
 * @param $request
 * @param $response
 * @param $config
 * @return unknown_type
 */
function onBoot(&$request, &$config)
{
	$params = &$request->getParams();
}