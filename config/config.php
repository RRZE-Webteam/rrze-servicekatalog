<?php

namespace RRZE\Servicekatalog\Config;

defined('ABSPATH') || exit;


function getShortcodeSettings(): array {
    return [
        'block' => [
            'blocktype' => 'rrze-servicekatalog/servicekatalog',
            'blockname' => 'servicekatalog',
            'title' => 'RRZE Servicekatalog',
            'category' => 'widgets',
            'icon' => 'portfolio',
            'tinymce_icon' => 'sharpen',
        ],
        'display' => [
            'values' => [
                'grid' => __( 'Grid', 'rrze-servicekatalog' ), // Abkürzung
                'list' => __( 'List', 'rrze-servicekatalog' ) // Ausgeschriebene Form
            ],
            'default' => 'grid',
            'field_type' => 'radio',
            'label' => __( 'Type of output', 'rrze-servicekatalog' ),
            'type' => 'string'
        ],
        'searchform' => [
            'field_type' => 'toggle',
            'label' => __( 'Show Searchform', 'rrze-servicekatalog' ),
            'type' => 'boolean',
            'default' => FALSE,
            'checked'   => FALSE
        ],
        'hide' => [
            'values' => [
                [
                    'id' => 'thumbnail',
                    'val' =>  __( 'Thumbnail', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'commitment',
                    'val' =>  __( 'Commitment', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'group',
                    'val' =>  __( 'Target Group', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'tag',
                    'val' =>  __( 'Tags', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'description',
                    'val' =>  __( 'Description', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-portal',
                    'val' =>  __( 'URL Portal', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-description',
                    'val' =>  __( 'URL Description', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-tutorial',
                    'val' =>  __( 'URL Tutorial', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-video',
                    'val' =>  __( 'URL Video', 'rrze-servicekatalog' )
                ],
            ],
            'field_type' => 'multi_select',
            'label' => __( 'Hide Elements', 'rrze-servicekatalog' ),
            'type' => 'array',
            'items'   => [
                'type' => 'string' // Variablentyp der auswählbaren Werte
            ]
        ],
        'target-group' => [
            'default' => '',
            'field_type' => 'text',
            'label' => __( 'Target Groups', 'rrze-servicekatalog' ),
            'type' => 'text'
        ],
        'commitment' => [
            'default' => '',
            'field_type' => 'text',
            'label' => __( 'Commitment', 'rrze-servicekatalog' ),
            'type' => 'text'
        ],
        'tag' => [
            'default' => '',
            'field_type' => 'text',
            'label' => __( 'Tags', 'rrze-servicekatalog' ),
            'type' => 'text'
        ],
        'id' => [
            'default' => NULL,
            'field_type' => 'text',
            'label' => __( 'IDs', 'rrze-servicekatalog' ),
            'type' => 'number'
        ],
    ];
}