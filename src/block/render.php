<?php

// Compatibility with Shortcode
$attributes['group'] = $attributes['selectedTargetGroups'] ?? '';
$attributes['commitment'] = $attributes['selectedCommitments'] ?? '';
$attributes['tag'] = $attributes['selectedTags'] ?? '';
$attributes['id'] = $attributes['selectedIDs'] ?? '';
$attributes['number'] = $attributes['numServices'] ?? '0';
//$attributes['show'] = $attributes['show'] ?? '';
$attributes['hide'] = $attributes['hideItems'] ?? '';
$attributes['display'] = $attributes['layout'] ?? 'grid';
$attributes['searchform'] = $attributes['showSearchform'] ?? '';
$attributes['orderby'] = $attributes['orderBy'] ?? '';
$attributes['pdf'] = $attributes['showPdf'] ?? '';
$attributes['display-switcher'] = $attributes['showDisplaySwitcher'] ?? '';
$attributes['teaser-length'] = $attributes['teaserLength'] ?? 50;

echo (new RRZE\Servicekatalog\Shortcodes\Servicekatalog)->shortcodeOutput($attributes);