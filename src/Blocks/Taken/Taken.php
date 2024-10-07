<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Taken;

use OWC\Zaaksysteem\Blocks\AbstractBlock;

class Taken extends AbstractBlock
{
    /**
     * Register block server side.
     */
    public function __construct()
    {
        $this->register('owc/mijn-taken', [
            'attributes' => [
                'zaakClient' => [
                    'type' => 'string',
                    'default' => 'openzaak',
                ],
                'view' => [
                    'type' => 'string',
                    'default' => 'default',
                ],
                'numberOfItems' => [
                    'type' => 'number',
                    'default' => 2,
                ],
                'title' => 'Mijn Taken',
                'style' => 'swf-extended-blocks-style',
                'editor_script' => 'swf-extended-blocks-script',
                'editor_style' => 'swf-extended-blocks-editor-style',
            ],
            'render_callback' => [new Block(), 'render'],
        ]);
    }
}
