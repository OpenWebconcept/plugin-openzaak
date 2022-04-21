<?php

declare(strict_types=1);

use function OWC\OpenZaak\Foundation\Helpers\view;

echo view('partials/zaak-collapse-button.php', [
    'title' => 'Melding geluidoverlast (dummy)',
    'id' => 'melding-geluidoverlast',
]);

$steps = [
    [
        'title' => 'Deelname aan geluidsonderzoek',
        'isChecked' => true,
        'isExpanded' => true,
        'substeps' => [
            [
                'title' => 'Aanmelding ontvangen',
                'isChecked' => true
            ]
        ]
    ],
    [
        'title' => 'Onderzoek naar geluidsoverlast',
        'isCurrent' => true,
        'isChecked' => true,
        'isExpanded' => true,
        'substeps' => [
            [
                'title' => 'Onderzoek naar geluidsoverlast',
            ],
            [
                'title' => 'Geluidsoverlast gemeten',
            ],
            [
                'title' => 'Onderzoeksresultaten verwerkt',
            ]
        ]
    ],
    [
        'title' => 'Uitvoeren van maatregelen',
    ],
    [
        'title' => 'Maatregelen zijn uitgevoerd',
    ]
];

?>

<div class="collapse" id="collapse-melding-geluidoverlast">
    <div class="zaak-collapse-content">
        <div class="zaak-meta">
            <?php echo view('partials/zaak-meta-item.php', [
                'label' => 'Zaaknummer',
                'value' => 'Testzaaknummer 01010101',
                'icon' => '<svg class="zaak-meta-item__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M464 32H48C21.5 32 0 53.5 0 80v64c0 8.8 7.2 16 16 16h16v272c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V160h16c8.8 0 16-7.2 16-16V80c0-26.5-21.5-48-48-48zm-16 400c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V160h384v272zm32-304H32V80c0-8.8 7.2-16 16-16h416c8.8 0 16 7.2 16 16v48zM204 256h104c6.6 0 12-5.4 12-12v-8c0-6.6-5.4-12-12-12H204c-6.6 0-12 5.4-12 12v8c0 6.6 5.4 12 12 12z"/></svg>'
            ]);
            ?>
            <?php echo view('partials/zaak-meta-item.php', [
                'label' => 'Startdatum',
                'value' => '20-01-2020',
                'icon' => '<svg class="zaak-meta-item__icon" viewBox="0 0 448 512"><path d="M400 64h-48V12c0-6.627-5.373-12-12-12h-8c-6.627 0-12 5.373-12 12v52H128V12c0-6.627-5.373-12-12-12h-8c-6.627 0-12 5.373-12 12v52H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zM48 96h352c8.822 0 16 7.178 16 16v48H32v-48c0-8.822 7.178-16 16-16zm352 384H48c-8.822 0-16-7.178-16-16V192h384v272c0 8.822-7.178 16-16 16zm-66.467-194.937l-134.791 133.71c-4.7 4.663-12.288 4.642-16.963-.046l-67.358-67.552c-4.683-4.697-4.672-12.301.024-16.985l8.505-8.48c4.697-4.683 12.301-4.672 16.984.024l50.442 50.587 117.782-116.837c4.709-4.671 12.313-4.641 16.985.068l8.458 8.527c4.672 4.709 4.641 12.313-.068 16.984z" /></svg>'
            ]);
            ?>
            <?php echo view('partials/zaak-meta-item.php', [
                'label' => 'Registratiedatum',
                'value' => '20-01-2020',
                'icon' => '<svg class="zaak-meta-item__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path d="M400 64h-48V12c0-6.6-5.4-12-12-12h-8c-6.6 0-12 5.4-12 12v52H128V12c0-6.6-5.4-12-12-12h-8c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM48 96h352c8.8 0 16 7.2 16 16v48H32v-48c0-8.8 7.2-16 16-16zm352 384H48c-8.8 0-16-7.2-16-16V192h384v272c0 8.8-7.2 16-16 16zM255.7 269.7l34.6 34.6c2.1 2.1 2.1 5.4 0 7.4L159.1 442.9l-35.1 5c-6.9 1-12.9-4.9-11.9-11.9l5-35.1 131.2-131.2c2-2 5.4-2 7.4 0zm75.2 1.4l-19.2 19.2c-2.1 2.1-5.4 2.1-7.4 0l-34.6-34.6c-2.1-2.1-2.1-5.4 0-7.4l19.2-19.2c6.8-6.8 17.9-6.8 24.7 0l17.3 17.3c6.8 6.8 6.8 17.9 0 24.7z" /></svg>'
            ]);
            ?>
            <?php echo view('partials/zaak-meta-item.php', [
                'label' => 'Status',
                'value' => 'Onderzoeksresultaten verwerkt',
                'icon' => '<svg class="zaak-meta-item__icon" viewBox="0 0 448 512">
                <path d="M224 480c-17.66 0-32-14.38-32-32.03h-32c0 35.31 28.72 64.03 64 64.03s64-28.72 64-64.03h-32c0 17.65-14.34 32.03-32 32.03zm209.38-145.19c-27.96-26.62-49.34-54.48-49.34-148.91 0-79.59-63.39-144.5-144.04-152.35V16c0-8.84-7.16-16-16-16s-16 7.16-16 16v17.56C127.35 41.41 63.96 106.31 63.96 185.9c0 94.42-21.39 122.29-49.35 148.91-13.97 13.3-18.38 33.41-11.25 51.23C10.64 404.24 28.16 416 48 416h352c19.84 0 37.36-11.77 44.64-29.97 7.13-17.82 2.71-37.92-11.26-51.22zM400 384H48c-14.23 0-21.34-16.47-11.32-26.01 34.86-33.19 59.28-70.34 59.28-172.08C95.96 118.53 153.23 64 224 64c70.76 0 128.04 54.52 128.04 121.9 0 101.35 24.21 138.7 59.28 172.08C421.38 367.57 414.17 384 400 384z" /></svg>'
            ]);
            ?>
        </div>

        <?php
        echo view('partials/zaak-process-steps.php', [
            'steps' => $steps,
        ]);

        echo view('partials/zaak-documents.php', [
            'documents' => [],
        ]);
        ?>
    </div>
</div>