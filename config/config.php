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
                'grid' => __( 'Grid', 'rrze-servicekatalog' ),
                'list' => __( 'List', 'rrze-servicekatalog' )
            ],
            'default' => 'grid',
            'field_type' => 'radio',
            'label' => __( 'Type of output', 'rrze-servicekatalog' ),
            'type' => 'string'
        ],
        'orderby' => [
            'values' => [
                'title' => __( 'Title', 'rrze-servicekatalog' ),
                'commitment' => __( 'Commitment Level', 'rrze-servicekatalog' ) ,
                'group' => __( 'Target Group', 'rrze-servicekatalog' ) ,
                'tag' => __( 'Tag', 'rrze-servicekatalog' ) ,
            ],
            'default' => 'title',
            'field_type' => 'radio',
            'label' => __( 'Order by', 'rrze-servicekatalog' ),
            'type' => 'string'
        ],
        'searchform' => [
            'field_type' => 'toggle',
            'label' => __( 'Show Searchform', 'rrze-servicekatalog' ),
            'type' => 'boolean',
            'default' => FALSE,
            'checked'   => FALSE
        ],
        'pdf_link' => [
            'field_type' => 'toggle',
            'label' => __( 'Show PDF Download Button', 'rrze-servicekatalog' ),
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
                    'id' => 'description',
                    'val' =>  __( 'Description', 'rrze-servicekatalog' )
                ],
                [
                    'id' => 'urls',
                    'val' =>  __( 'All URLs', 'rrze-servicekatalog' )
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
                    'val' =>  __( 'URL Video Tutorial', 'rrze-servicekatalog' )
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

            ],
            'field_type' => 'multi_select',
            'label' => __( 'Hide Elements', 'rrze-servicekatalog' ),
            'type' => 'array',
            'items'   => [
                'type' => 'string' // Variablentyp der auswählbaren Werte
            ]
        ],
        'teaser_length' => [
            'label' => __( 'Teaser Length', 'rrze-servicekatalog' ),
            //'description' => __('', 'rrze-servicekatalog'),
            'field_type' => 'text_small',
            'default' => '50',
            'attributes' => [
                'type' => 'number',
            ],
            'type' => 'number',
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