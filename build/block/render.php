<?php
//print "<pre>"; var_dump($attributes); print "</pre>";

// Compatibility with Shortcode
$attributes['display'] = $attributes['layout'] ?? 'grid';
$attributes['orderby'] = $attributes['orderBy'] ?? '';
$attributes['teaser-length'] = $attributes['teaserLength'] ?? 50;

$attributes['searchform'] = $attributes['showSearchform'] ?? '';
$attributes['display-switcher'] = $attributes['showDisplaySwitcher'] ?? '';
$attributes['pdf'] = $attributes['showPdf'] ?? '';
$visibleItems = ["thumbnail", "commitment", "group", "tag", "description", "url-portal", "url-description", "url-tutorial", "url-video", "urls"];
$attributes['hide'] = array_diff($visibleItems, $attributes['selectedShowItems'] ?? []);

$attributes['group'] = isset($attributes['selectedTargetGroups']) ? implode(',', $attributes['selectedTargetGroups']) : '';
$attributes['commitment'] = isset($attributes['selectedCommitments']) ? implode(',', $attributes['selectedCommitments']) : '';
$attributes['tag'] = isset($attributes['selectedTags']) ? implode(',', $attributes['selectedTags']) : '';
$attributes['id'] = isset($attributes['selectedServices']) ? implode(',', $attributes['selectedServices']) : '';
$attributes['number'] = $attributes['numServices'] ?? '0';

echo (new RRZE\Servicekatalog\Shortcodes\Servicekatalog)->shortcodeOutput($attributes);
