<?php

class NgoisaonetCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getNgoisao();
	}

}