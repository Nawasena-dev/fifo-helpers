1. Tambahakan pada .env
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Nawasena-dev/fifo-helpers"
        }

    ]
2. jalankan 
    composer require nawasena/fifo-helpers:v1.0.2


QueueNumber::generate([
    'table' => 'registrations',
    'column' => 'queue_number',
    'columnDate' => 'date',
    'format' => '{serial}',
    'padding' => 5,
    'date' => '2025-01-01',
]);

RegistrationNumber::generate([
    'table' => 'registrations',
    'column' => 'registration_number',
    'columnDate' => 'date',
    'format' => '{prefix}-{y}-{serial}',
    'padding' => $_regSetting->padding,
    'date' => '2025-01-01',
    'resetBy' => 'yearly',
]);
