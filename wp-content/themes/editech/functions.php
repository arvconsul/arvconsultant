<?php
if (version_compare($GLOBALS['wp_version'], '4.7-alpha', '<')) {
    require get_theme_file_path('inc/back-compat.php');

    return;
}
if (is_admin()) {
    require get_theme_file_path('inc/admin/class-admin.php');
}

require get_theme_file_path('inc/tgm-plugins.php');
require get_theme_file_path('inc/template-tags.php');
require get_theme_file_path('inc/template-functions.php');
require get_theme_file_path('inc/class-main.php');
require get_theme_file_path('inc/starter-settings.php');

if (!class_exists('EditechCore')) {
    // Blog Sidebar
    require get_theme_file_path('inc/class-sidebar.php');
}