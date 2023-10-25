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
                    'val' =>  __( 'Commitment Level', 'rrze-servicekatalog' )
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
                    'val' =>  __( 'Portal', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-description',
                    'val' =>  __( 'Service Description', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-tutorial',
                    'val' =>  __( 'Tutorial', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'url-video',
                    'val' =>  __( 'Video Tutorial', 'rrze-servicekatalog' )
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
            'label' => __( 'Commitment Level', 'rrze-servicekatalog' ),
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
            'label' => __( 'Services', 'rrze-servicekatalog' ),
            'type' => 'number'
        ],
    ];
}