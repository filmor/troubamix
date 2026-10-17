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
	private const MARKER_EXTENSION = '.tbmx';
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
		foreach ($userFolder->search('tbmx') as $marker) {
			if (!$marker instanceof File || !$this->isMarkerFile($marker)) {
				continue;
			}

			$metadata = $this->readMetadata($marker);
			if ($metadata !== null) {
				$songs[] = $this->presentSong($marker, $metadata);
			}
		}

		usort($songs, static fn (array $a, array $b): int => strcasecmp($a['folderName'], $b['folderName']));
		return new JSONResponse($songs);
	}

	#[NoAdminRequired]
	public function show(int $folderId): JSONResponse|NotFoundResponse {
		$marker = $this->getSongMarker($folderId);
		if ($marker === null) {
			return new NotFoundResponse();
		}

		$metadata = $this->readMetadata($marker);
		if ($metadata === null) {
			return new NotFoundResponse();
		}

		return new JSONResponse($this->presentSong($marker, $metadata));
	}

	#[NoAdminRequired]
	public function file(int $folderId, string $fileName): FileDisplayResponse|NotFoundResponse {
		if (!preg_match('/^[A-Za-z0-9_-]+\.mp3$/D', $fileName)) {
			return new NotFoundResponse();
		}

		$marker = $this->getSongMarker($folderId);
		if ($marker === null) {
			return new NotFoundResponse();
		}

		$metadata = $this->readMetadata($marker);
		$folder = $marker->getParent();
		if ($metadata === null || !$folder instanceof Folder || !$this->isTrackFile($metadata, $fileName) || !$folder->nodeExists($fileName)) {
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

	private function getSongMarker(int $fileId): ?File {
		if ($fileId < 1) {
			return null;
		}

		$userFolder = $this->getUserFolder();
		if ($userFolder === null) {
			return null;
		}

		foreach ($userFolder->getById($fileId) as $node) {
			if ($node instanceof File && $this->isMarkerFile($node) && $this->readMetadata($node) !== null) {
				return $node;
			}
		}

		return null;
	}

	private function readMetadata(File $marker): ?array {
		if (!$marker->isReadable() || $marker->getSize() > self::MAX_METADATA_SIZE) {
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

	private function isMarkerFile(File $file): bool {
		return str_ends_with(strtolower($file->getName()), self::MARKER_EXTENSION);
	}

	private function isTrackFile(array $metadata, string $fileName): bool {
		foreach ($metadata['tracks'] as $track) {
			if ($fileName === $metadata['name'] . '-' . $track['id'] . '.mp3') {
				return true;
			}
		}

		return false;
	}

	private function presentSong(File $marker, array $metadata): array {
		$folder = $marker->getParent();

		return array_merge($metadata, [
			'id' => (string)$marker->getId(),
			'folderName' => $folder instanceof Folder ? $folder->getName() : '',
		]);
	}
}
