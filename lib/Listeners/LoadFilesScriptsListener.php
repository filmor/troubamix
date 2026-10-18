<?php

declare(strict_types=1);

namespace OCA\Troubamix\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class LoadFilesScriptsListener implements IEventListener {
	public function handle(Event $event): void {
		Util::addScript('troubamix', 'troubamix');
	}
}
