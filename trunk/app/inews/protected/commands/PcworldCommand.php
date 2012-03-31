<?php

class PcworldCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getPcWorld();
	}

}