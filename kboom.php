<?php
/*
Plugin Name: KBoom
Description: Knowledge Base Taxonomy and Custom Post Type
Version: 1.0
Author: randmanrjr
Author URI: https://github.com/randmanrjr
*/


add_action('init','kboom_post_types');

function kboom_post_types() {

    register_post_type('kaboom_post_type',
        array(
            'labels'                => array(
                'name'              => 'Knowledge Base Articles',
                'menu_name'         => 'KB Articles',
                'name_admin_bar'    => 'KB Articles'
            ),
            'singular_label'        => 'Knowledge Base Article',
            'public'                => true,
            'show_ui'               => true,
            'menu_position'         => 20,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'show_in_menu'          => true,
            'rewrite'               => array('slug' => 'kbarticle'),
            'supports'              => array('title', 'editor', 'author', 'revisions', 'comments', 'thumbnail', 'excerpt', 'page-attributes'),
            'taxonomies'            => array('kbarticles')
        ));
}

add_action('init', 'create_kbarticles_taxonomy');

function create_kbarticles_taxonomy() {
    $labels = array(
        'name'                          => 'KB Articles',
        'sigular_name'                  => 'KB Article',
        'search_items'                  => 'Search Knowledge Base',
        'all_items'                     => 'All KB Articles',
        'edit_item'                     => 'Edit KB Article',
        'update_item'                   => 'Update KB Article',
        'add_new_item'                  => 'Add New KB Article',
        'new_item_name'                 => 'New KB Article Name',
        'menu_name'                     => 'KB Article',
        'view_item'                     => 'View KB Article',
        'popular_items'                 => 'Popular KB Articles',
        'separate_items_with_commas'    => 'Separate KB Articles with commas',
        'add_or_remove_items'           => 'Add or remove KB Articles',
        'choose_from_most_used'         => 'Choose from the most accessed KB Aticles',
        'not_found'                     => 'No matching KB Article'
    );
    register_taxonomy(
        'kbarticles',
        'kbarticle',
        array(
            'label'                     => __('KB Article'),
            'hierarchical'              => true,
            'labels'                    => $labels
        )
    );
}