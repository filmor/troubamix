<?php

declare(strict_types=1);

namespace OCA\Troubamix\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\IRequest;
use OCP\Util;

class PageController extends Controller {
	public function __construct(string $appName, IRequest $request) {
		parent::__construct($appName, $request);
	}

	#[NoAdminRequired]
	public function index(): TemplateResponse {
		return $this->renderPage(null);
	}

	#[NoAdminRequired]
	public function mix(string $song): TemplateResponse|NotFoundResponse {
		if (!ctype_digit($song) || (int)$song < 1) {
			return new NotFoundResponse();
		}

		return $this->renderPage($song);
	}

	private function renderPage(?string $song): TemplateResponse {
		Util::addScript($this->appName, 'troubamix');
		Util::addStyle($this->appName, 'troubamix');

		return new TemplateResponse($this->appName, 'index', [
			'config' => [
				'appUrl' => rtrim(Util::linkToRoute($this->appName . '.page.index'), '/') . '/',
				'songsUrl' => rtrim(Util::linkToRoute($this->appName . '.song.index'), '/'),
				'song' => $song,
			],
		]);
	}
}
