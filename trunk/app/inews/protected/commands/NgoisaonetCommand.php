<?php

class VnEconomyCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getNgoisao();
	}

}