<?php
/**
 * Plugin Name: Directorist - Admin Listing Access
 * Description: Show published administrator-authored listings without an assigned pricing package. Temporary companion for Directorist Pricing Plans 4.x.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Requires Plugins: directorist, directorist-pricing-plans
 * Author: Sovware
 * License: GPL-3.0-or-later
 */

defined( 'ABSPATH' ) || exit;

final class Sovware_Directorist_Admin_Listing_Access {
    public static function boot() {
        $fields = 'DirectoristPricingPlan\\App\\Providers\\FormFields';
        $access = 'DirectoristPricingPlan\\App\\Providers\\PlanServiceProvider';
        $targets = [
            'directorist_single_listings_contents' => [ $fields => 'single_listings_contents', $access => 'restrict_single_listing_sections' ],
            'directorist_single_listing_header' => [ $fields => 'single_listing_header', $access => 'restrict_single_listing_header' ],
            'directorist_single_listing_thumbnails' => [ $fields => 'listing_thumbnails' ],
            'directorist_listing_archive_thumbnails' => [ $fields => 'listing_thumbnails' ],
            'the_content' => [ $access => 'restrict_package_less_listing_content' ],
            'directorist_custom_single_listing_pre_page_content' => [ $access => 'restrict_custom_single_listing_content' ],
            'sidebars_widgets' => [ $access => 'restrict_single_listing_sidebar' ],
            'directorist_single_listing_after_title' => [ $access => 'render_listing_unavailable_notice' ],
        ];

        foreach ( $targets as $hook => $methods ) {
            // Inspect at invocation time, after the extension has registered its providers.
            add_filter( $hook, static function ( $value ) use ( $hook, $methods ) {
                self::wrap_callbacks( $hook, $methods );
                return $value;
            }, PHP_INT_MIN );
        }
    }

    private static function eligible( $listing_id ) {
        if ( ! function_exists( 'directorist_get_listing_package' ) ) {
            return false;
        }
        $listing = get_post( (int) $listing_id );
        $post_type = defined( 'ATBDP_POST_TYPE' ) ? ATBDP_POST_TYPE : 'at_biz_dir';
        if ( ! $listing || $post_type !== $listing->post_type || 'publish' !== $listing->post_status ) {
            return false;
        }
        $author = get_userdata( (int) $listing->post_author );
        return $author && in_array( 'administrator', (array) $author->roles, true )
            && ! directorist_get_listing_package( (int) $listing->ID );
    }

    private static function wrap_callbacks( $hook, array $methods ) {
        global $wp_filter;
        if ( ! isset( $wp_filter[ $hook ] ) || ! $wp_filter[ $hook ] instanceof WP_Hook ) {
            return;
        }
        foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
            foreach ( $callbacks as $id => $entry ) {
                $callback = $entry['function'];
                if ( ! is_array( $callback ) || ! is_object( $callback[0] ) ) {
                    continue;
                }
                $class = get_class( $callback[0] );
                if ( ! isset( $methods[ $class ] ) || $methods[ $class ] !== $callback[1] ) {
                    continue;
                }
                // Preserve priority, callback identity, accepted arguments and sibling order.
                $wp_filter[ $hook ]->callbacks[ $priority ][ $id ]['function'] = static function ( ...$args ) use ( $callback, $hook ) {
                    if ( in_array( $hook, [ 'directorist_single_listings_contents', 'directorist_single_listing_header' ], true ) ) {
                        $listing_id = (int) ( $args[1]['listing_id'] ?? 0 );
                    } elseif ( in_array( $hook, [ 'directorist_single_listing_thumbnails', 'directorist_listing_archive_thumbnails' ], true ) ) {
                        $listing_id = (int) ( $args[1] ?? 0 );
                    } elseif ( 'directorist_single_listing_after_title' === $hook ) {
                        $listing_id = (int) ( $args[0] ?? 0 );
                    } else {
                        $listing_id = 0;
                    }
                    $listing_id = $listing_id ?: get_queried_object_id();
                    if ( self::eligible( $listing_id ) ) {
                        return 'directorist_single_listing_after_title' === $hook ? null : ( $args[0] ?? null );
                    }
                    return call_user_func_array( $callback, $args );
                };
            }
        }
    }
}

Sovware_Directorist_Admin_Listing_Access::boot();
