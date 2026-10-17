<?php

declare(strict_types=1);

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'page#mix', 'url' => '/mix/{song}', 'verb' => 'GET'],
		['name' => 'song#index', 'url' => '/api/songs', 'verb' => 'GET'],
		['name' => 'song#show', 'url' => '/api/songs/{folderId}', 'verb' => 'GET'],
		['name' => 'song#file', 'url' => '/api/songs/{folderId}/files/{fileName}', 'verb' => 'GET'],
	],
];
