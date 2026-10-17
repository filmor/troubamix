<?php

declare(strict_types=1);

namespace OCA\Troubamix\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\INavigationManager;
use OCP\Util;

class Application extends App implements IBootstrap {
	public const APP_ID = 'troubamix';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(
			\OCA\Files\Event\LoadAdditionalScriptsEvent::class,
			\OCA\Troubamix\Listeners\LoadFilesScriptsListener::class,
		);
	}

	public function boot(IBootContext $context): void {
	}

	public function registerNavigationEntry(): void {
		$this->getContainer()->get(INavigationManager::class)->add(static function (): array {
			return [
				'id' => self::APP_ID,
				'app' => self::APP_ID,
				'order' => 10,
				'href' => Util::linkToRoute(self::APP_ID . '.page.index'),
				'icon' => Util::imagePath(self::APP_ID, 'app.svg'),
				'name' => 'Troubamix',
			];
		});
	}
}
