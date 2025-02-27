<?php
echo 'test';

//http://localhost/wp/fau-einrichtungen/wp-json/wp/v2/block-renderer/rrze/servicekatalog?context=edit&attributes%5BselectedServices%5D=0&attributes%5BnumServices%5D=-1&attributes%5BorderBy%5D=&attributes%5Blayout%5D=grid&attributes%5BshowSearchform%5D=false&attributes%5BshowPdf%5D=false&attributes%5BshowDisplaySwitcher%5D=false&attributes%5BteaserLength%5D=50&post_id=4209&_locale=user
/*
// Compatibility with Shortcode
$attributes['group'] = isset($attributes['selectedTargetGroups']) ? implode(',', $attributes['selectedTargetGroups']) : '';
$attributes['commitment'] = isset($attributes['selectedCommitments']) ? implode(',', $attributes['selectedCommitments']) : '';
$attributes['tag'] = isset($attributes['selectedTags']) ? implode(',', $attributes['selectedTags']) : '';
$attributes['id'] = isset($attributes['selectedIDs']) ? implode(',', $attributes['selectedIDs']) : '';
$attributes['number'] = $attributes['numServices'] ?? '0';
if ($attributes['number'])
//$attributes['show'] = $attributes['show'] ?? '';
$attributes['hide'] = isset($attributes['selectedHiddenItems']) ? implode(',', $attributes['selectedHiddenItems']) : '';
$attributes['display'] = $attributes['layout'] ?? 'grid';
$attributes['searchform'] = $attributes['showSearchform'] ?? '';
$attributes['orderby'] = $attributes['orderBy'] ?? '';
$attributes['pdf'] = $attributes['showPdf'] ?? '';
$attributes['display-switcher'] = $attributes['showDisplaySwitcher'] ?? '';
$attributes['teaser-length'] = $attributes['teaserLength'] ?? 50;

echo (new RRZE\Servicekatalog\Shortcodes\Servicekatalog)->shortcodeOutput($attributes);*/