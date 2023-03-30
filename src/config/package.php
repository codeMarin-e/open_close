<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\OpenClose\Database\Seeders\MarinarOpenCloseInstallSeeder"',
		],
        'remove' => [
            'php artisan db:seed --class="\Marinar\OpenClose\Database\Seeders\MarinarOpenCloseRemoveSeeder"',
        ]
	];
