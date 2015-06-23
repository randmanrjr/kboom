<?php
/*
Plugin Name: KBoom
Description: Knowledge Base Taxonomy and Custom Post Type
Version: 1.0
Author: randmanrjr
Author URI: https://github.com/randmanrjr
*/


add_action('init','kaboom_post_types');

function kaboom_post_types() {

    register_post_type('kaboom_post_type',
        array(
            'labels'                => array(
                'name'              => 'Knowledge Base Articles',
                'menu_name'         => 'KB Articles',
                'name_admin_bar'    => 'KB Article'
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
            'taxonomies'            => array('')
        ));
}