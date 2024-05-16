<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

use RRZE\Servicekatalog\Shortcodes\Servicekatalog;

if (isset($_GET['format']) && $_GET['format'] == 'embedded') {
    get_template_part('template-parts/index', 'embedded');
    return;
}
if ( is_active_sidebar( 'news-sidebar' ) ) { 
    fau_use_sidebar(true);    
}
get_header();
global $wp_query;
$queryVars = $wp_query->query_vars;

$atts = [];
$title = __('Services', 'rrze-servicekatalog');
if (isset($queryVars['rrze-service-target-group']) && $queryVars['rrze-service-target-group'] != '') {
    $term = get_term_by('slug', sanitize_title($queryVars['rrze-service-target-group']), 'rrze-service-target-group');
    $title = __('Target Group', 'rrze-servicekatalog') . ': ' . $term->name;
    $atts['group'] = sanitize_title($queryVars['rrze-service-target-group']);
} elseif (isset($queryVars['rrze-service-commitment']) && $queryVars['rrze-service-commitment'] != '') {
    $term = get_term_by('slug', sanitize_title($queryVars['rrze-service-commitment']), 'rrze-service-commitment');
    $title = __('Use', 'rrze-servicekatalog') . ': ' . $term->name;
    $atts['commitment'] = sanitize_title($queryVars['rrze-service-commitment']);
} elseif (isset($queryVars['rrze-service-tag']) && $queryVars['rrze-service-tag'] != '') {
    $term = get_term_by('slug', sanitize_title($queryVars['rrze-service-tag']), 'rrze-service-tag');
    $title = __('Tag', 'rrze-servicekatalog') . ': ' . $term->name;
    $atts['tag'] = sanitize_title($queryVars['rrze-service-tag']);
}

$atts['orderby'] = 'commitment';
$atts['pdf'] = 'false';

$settings = get_option('rrze-servicekatalog-settings');

$atts['display'] = isset($settings['display']) ? sanitize_text_field($settings['display']) : 'grid';
$atts['searchform'] = isset($settings['searchform']) ? sanitize_text_field($settings['searchform']) : '';
if (isset($settings['hide'])) {
    array_walk($settings['hide'], 'sanitize_text_field');
    $atts['hide'] = implode(',', $settings['hide']);
}
?>

    <div id="content">
	    <div class="content-container">
		    <div class="post-row">
			    <main class="entry-content">

                    <h1 id="maintop" class="mobiletitle"><span class="screen-reader-text"><?php echo __('Services', 'rrze-servicekatalog') . ' / '; ?> </span><?php echo $title; ?></h1>

                    <?php
                    $servicekatalog = new Servicekatalog();
                    echo $servicekatalog->shortcodeOutput($atts);
                    ?>

			    </main>
		    </div>    
	    </div>
	
    </div>
<?php 
get_footer(); 

