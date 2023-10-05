<?php

namespace RRZE\Servicekatalog;

class Util {

    /**
     * Returns a meta value out of a given get_post_meta($post_id) array.
     *
     * @param array $meta Post meta array (result of get_post_meta($post_id) query)
     * @param string $key Meta key
     *
     * @return string|array
     */
    public static function getMeta( $meta, $key ) {
        if ( ! isset( $meta[ $key ] ) ) {
            return '';
        }
        if ( str_starts_with( $meta[ $key ][ 0 ], 'a:' ) ) {
            return unserialize( $meta[ $key ][ 0 ] );
        } else {
            return $meta[ $key ][ 0 ];
        }
    }

    public static function addCSSVars() {
        $options = get_option( 'rrze_projects_options' );
        $constants = getConstants();
        $accentColor = ( isset( $options[ 'accent-color' ] ) && $options[ 'accent-color' ] != '' ) ? $options[ 'accent-color' ] : $constants[ 'default-accent-color' ];
        $linkColor = ( isset( $options[ 'link-color' ] ) && $options[ 'link-color' ] != '' ) ? $options[ 'link-color' ] : $constants[ 'default-link-color' ];
        $linkHoverColor = Util::adjustBrightness( $linkColor, - .4 );
        echo '<style type="text/css">
:root {
    --rrze-projects-color-accent: ' . $accentColor . ';
    --rrze-projects-color-link: ' . $linkColor . ';
    --rrze-projects-color-link-hover: ' . $linkHoverColor . ';
}
</style>';
    }

    /**
     * Calculate the corresponding contrast color (black or white) for a given
     * background color to match contrast requirements.
     *
     * @param string $color
     *
     * @return string
     */
    public static function calculateContrastColor( $color ) {
        $color = str_replace( '#', '', $color );
        $r = hexdec( substr( $color, 0, 2 ) );
        $g = hexdec( substr( $color, 2, 2 ) );
        $b = hexdec( substr( $color, 4, 2 ) );
        $d = '#000';
        // Counting the perceptive luminance - human eye favors green color...
        $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
        if ( $luminance < 0.5 ) {
            $d = '#fff';
        }

        return $d;
    }

    /**
     * Increases or decreases the brightness of a color by a percentage of the
     * current brightness.
     *
     * @param string $hexCode Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
     * @param float $adjustPercent A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
     *
     * @return  string
     *
     * @author  maliayas
     */
    public static function adjustBrightness( $hexCode, $adjustPercent ) {
        $hexCode = ltrim( $hexCode, '#' );

        if ( strlen( $hexCode ) == 3 ) {
            $hexCode = $hexCode[ 0 ] . $hexCode[ 0 ] . $hexCode[ 1 ] . $hexCode[ 1 ] . $hexCode[ 2 ] . $hexCode[ 2 ];
        }

        $hexCode = array_map( 'hexdec', str_split( $hexCode, 2 ) );

        foreach ( $hexCode as & $color ) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil( $adjustableLimit * $adjustPercent );

            $color = str_pad(dechex(intval($color + $adjustAmount)), 2, '0', STR_PAD_LEFT);
        }

        return '#' . implode( $hexCode );
    }

    public static function compilePostTypeCapabilities($singular = 'post', $plural = 'posts') {
        return [
            'edit_post'		 => "edit_$singular",
            'read_post'		 => "read_$singular",
            'delete_post'		 => "delete_$singular",
            'edit_posts'		 => "edit_$plural",
            'edit_others_posts'	 => "edit_others_$plural",
            'publish_posts'		 => "publish_$plural",
            'read_private_posts'	 => "read_private_$plural",
            'read'                   => "read",
            'delete_posts'           => "delete_$plural",
            'delete_private_posts'   => "delete_private_$plural",
            'delete_published_posts' => "delete_published_$plural",
            'delete_others_posts'    => "delete_others_$plural",
            'edit_private_posts'     => "edit_private_$plural",
            'edit_published_posts'   => "edit_published_$plural",
            'create_posts'           => "edit_$plural",
        ];
    }

}