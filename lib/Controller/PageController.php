<?php

declare(strict_types=1);

namespace OCA\Troubamix\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Exceptions\NotFoundException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Attribute\NoAdminRequired;
use OCP\IRequest;
use OCP\Util;

class PageController extends Controller {
	private const SONGS = ['owner', 'pdm'];

	public function __construct(string $appName, IRequest $request) {
		parent::__construct($appName, $request);
	}

	#[NoAdminRequired]
	public function index(): TemplateResponse {
		return $this->renderPage(null);
	}

	#[NoAdminRequired]
	public function mix(string $song): TemplateResponse {
		if (!in_array($song, self::SONGS, true)) {
			throw new NotFoundException('Song not found');
		}

		return $this->renderPage($song);
	}

	private function renderPage(?string $song): TemplateResponse {
		Util::addScript($this->appName, 'troubamix');
		Util::addStyle($this->appName, 'troubamix');

		return new TemplateResponse($this->appName, 'index', [
			'config' => [
				'appUrl' => rtrim(Util::linkToRoute($this->appName . '.page.index'), '/') . '/',
				'songsUrl' => rtrim(Util::linkTo($this->appName, 'static/songs'), '/'),
				'song' => $song,
			],
		]);
	}
}
