<?php

declare(strict_types=1);

namespace OCA\Troubamix\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IUserSession;

class SongController extends Controller {
	private const MARKER_FILE = 'troubamix.json';
	private const MAX_METADATA_SIZE = 1048576;

	public function __construct(
		string $appName,
		IRequest $request,
		private IRootFolder $rootFolder,
		private IUserSession $userSession,
	) {
		parent::__construct($appName, $request);
	}

	#[NoAdminRequired]
	public function index(): JSONResponse {
		$userFolder = $this->getUserFolder();
		if ($userFolder === null) {
			return new JSONResponse([]);
		}

		$songs = [];
		foreach ($userFolder->search(self::MARKER_FILE) as $marker) {
			if (!$marker instanceof File || $marker->getName() !== self::MARKER_FILE) {
				continue;
			}

			$folder = $marker->getParent();
			if (!$folder instanceof Folder) {
				continue;
			}

			$metadata = $this->readMetadata($folder);
			if ($metadata !== null) {
				$songs[] = $this->presentSong($folder, $metadata);
			}
		}

		usort($songs, static fn (array $a, array $b): int => strcasecmp($a['folderName'], $b['folderName']));
		return new JSONResponse($songs);
	}

	#[NoAdminRequired]
	public function show(int $folderId): JSONResponse|NotFoundResponse {
		$folder = $this->getSongFolder($folderId);
		if ($folder === null) {
			return new NotFoundResponse();
		}

		$metadata = $this->readMetadata($folder);
		if ($metadata === null) {
			return new NotFoundResponse();
		}

		return new JSONResponse($this->presentSong($folder, $metadata));
	}

	#[NoAdminRequired]
	public function file(int $folderId, string $fileName): FileDisplayResponse|NotFoundResponse {
		if (!preg_match('/^[A-Za-z0-9_-]+\.mp3$/D', $fileName)) {
			return new NotFoundResponse();
		}

		$folder = $this->getSongFolder($folderId);
		if ($folder === null) {
			return new NotFoundResponse();
		}

		$metadata = $this->readMetadata($folder);
		if ($metadata === null || !$this->isTrackFile($metadata, $fileName) || !$folder->nodeExists($fileName)) {
			return new NotFoundResponse();
		}

		$file = $folder->get($fileName);
		if (!$file instanceof File || !$file->isReadable()) {
			return new NotFoundResponse();
		}

		return new FileDisplayResponse($file, headers: ['Content-Type' => 'audio/mpeg']);
	}

	private function getUserFolder(): ?Folder {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return null;
		}

		return $this->rootFolder->getUserFolder($user->getUID());
	}

	private function getSongFolder(int $folderId): ?Folder {
		if ($folderId < 1) {
			return null;
		}

		$userFolder = $this->getUserFolder();
		if ($userFolder === null) {
			return null;
		}

		foreach ($userFolder->getById($folderId) as $node) {
			if ($node instanceof Folder && $node->nodeExists(self::MARKER_FILE)) {
				return $node;
			}
		}

		return null;
	}

	private function readMetadata(Folder $folder): ?array {
		if (!$folder->nodeExists(self::MARKER_FILE)) {
			return null;
		}

		$marker = $folder->get(self::MARKER_FILE);
		if (!$marker instanceof File || !$marker->isReadable() || $marker->getSize() > self::MAX_METADATA_SIZE) {
			return null;
		}

		try {
			$metadata = json_decode($marker->getContent(), true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException) {
			return null;
		}

		if (!is_array($metadata)
			|| !isset($metadata['name'], $metadata['long_name'], $metadata['tracks'])
			|| !is_string($metadata['name'])
			|| !preg_match('/^[A-Za-z0-9_-]+$/D', $metadata['name'])
			|| !is_string($metadata['long_name'])
			|| !is_array($metadata['tracks'])
			|| $metadata['tracks'] === []) {
			return null;
		}

		foreach ($metadata['tracks'] as $track) {
			if (!is_array($track)
				|| !isset($track['id'], $track['name'])
				|| !is_string($track['id'])
				|| !preg_match('/^[A-Za-z0-9_-]+$/D', $track['id'])
				|| !is_string($track['name'])) {
				return null;
			}
		}

		return $metadata;
	}

	private function isTrackFile(array $metadata, string $fileName): bool {
		foreach ($metadata['tracks'] as $track) {
			if ($fileName === $metadata['name'] . '-' . $track['id'] . '.mp3') {
				return true;
			}
		}

		return false;
	}

	private function presentSong(Folder $folder, array $metadata): array {
		return array_merge($metadata, [
			'id' => (string)$folder->getId(),
			'folderName' => $folder->getName(),
		]);
	}
}
