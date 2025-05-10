<?php
/*
Plugin Name: Simple Floating Buttons
Plugin URI: https://www.linkedin.com/in/binhnn/
Description: A simple and effective WordPress plugin to add floating Contact and Back to Top buttons.
Version: 1.0
Author: Binhnn
Author URI: https://binhnn.dev/
*/

/**
 * 1. Cài đặt các giá trị mặc định khi kích hoạt plugin
 */
function fabp_default_options() {
    $default_options = array(
        'zalo_link'      => '#',
        'phone_link'     => '#',
        'messenger_link' => '#',
    );
    if ( ! get_option('fabp_options') ) {
        add_option('fabp_options', $default_options);
    }
}
register_activation_hook(__FILE__, 'fabp_default_options');

/**
 * 2. Thêm menu cài đặt vào Dashboard
 */
function fabp_add_menu() {
    add_menu_page(
        'Multi-Contact Settings',  // Tiêu đề trang
        'Multi-Contact',           // Tên menu
        'manage_options',                // Quyền truy cập
        'fabp-settings',                 // Slug menu
        'fabp_settings_page',            // Hàm hiển thị trang cài đặt
        'dashicons-admin-generic',       // Icon
        81                               // Vị trí menu
    );
}
add_action('admin_menu', 'fabp_add_menu');
/**
 * 3. Xoá mục menu của plugin khỏi thanh bên trái.
 */
function fabp_remove_admin_menu_item() {
    remove_menu_page('fabp-settings');
}
add_action('admin_menu', 'fabp_remove_admin_menu_item', 999);
/**
 * 3. Hàm hiển thị trang cài đặt
 */
function fabp_settings_page() {
    if ( ! current_user_can('manage_options') ) {
        wp_die(__('Bạn không có quyền truy cập trang này.'));
    }
    
    // Lấy các giá trị đang lưu
    $options = get_option('fabp_options');
    
    // Cập nhật dữ liệu nếu form được submit
    if ( isset($_POST['fabp_submit']) ) {
        check_admin_referer('fabp_settings_verify');
        
        $options['zalo_link']      = sanitize_text_field($_POST['zalo_link']);
        $options['phone_link']     = sanitize_text_field($_POST['phone_link']);
        $options['messenger_link'] = sanitize_text_field($_POST['messenger_link']);
        update_option('fabp_options', $options);
        
        echo '<div class="updated"><p>Cập nhật thành công!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Multi-Contact Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('fabp_settings_verify'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Zalo Link</th>
                    <td>
                        <input type="text" name="zalo_link" value="<?php echo esc_attr($options['zalo_link']); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Phone Link</th>
                    <td>
                        <input type="text" name="phone_link" value="<?php echo esc_attr($options['phone_link']); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Messenger Link</th>
                    <td>
                        <input type="text" name="messenger_link" value="<?php echo esc_attr($options['messenger_link']); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            <?php submit_button('Lưu thay đổi', 'primary', 'fabp_submit'); ?>
        </form>
        <hr>
        <h2>Thông tin Plugin Multi-Contact</h2>
        <p><strong>Version:</strong> 1.0</p>
        <p><strong>Tác giả:</strong> Binhnn</p>
        <p><strong>Mail:</strong> <a href="mailto:gautrucmarketing@gmail.com">gautrucmarketing@gmail.com</a></p>
        <p><strong>Phone:</strong> <a href="tel:0876011134">0876011134</a></p>
    </div>
    <?php
}

/**
 * 4. Hàm chèn HTML Multi-Contact vào trang (sử dụng hook wp_footer)
 */
function fabp_display_float_action_button() {
    $options = get_option('fabp_options');
    ?>
    <nav class="float-action-button" style="z-index:9999; position: fixed; bottom: 60px; right: 5px; margin: 1em;">
      <a href="<?php echo esc_url($options['zalo_link']); ?>" class="buttons zalo" title="Zalo">
          <i class="fa fa-zalo"></i>
      </a>
      <a href="<?php echo esc_url($options['phone_link']); ?>" class="buttons" title="Phone">
          <i class="fas fa-phone"></i>
      </a>
      <a href="<?php echo esc_url($options['messenger_link']); ?>" class="buttons" title="Messenger">
          <i class="fab fa-facebook-messenger"></i>
      </a>
      <a href="#" class="buttons main-button">
          <i class="fa fa-times"></i>
          <i class="fa fa-share-alt"></i>
      </a>
    </nav>
    <?php
}
add_action('wp_footer', 'fabp_display_float_action_button');

/**
 * 5. Enqueue CSS và JS (nếu cần)
 */
function fabp_enqueue_scripts() {
    wp_enqueue_style('fabp-style', plugin_dir_url(__FILE__) . 'css/fabp-style.css');
    // Nếu có JS, bạn có thể bổ sung:
    // wp_enqueue_script('fabp-script', plugin_dir_url(__FILE__) . 'js/fabp-script.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'fabp_enqueue_scripts');
// Thêm mục menu vào thanh Admin Bar
function fabp_admin_bar_menu( $wp_admin_bar ) {
    // Kiểm tra quyền, chỉ hiển thị cho quản trị viên
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $args = array(
        'id'    => 'fabp-settings',   // ID cho mục menu
        'title' => 'Multi-Contact', // Tiêu đề hiển thị
        'href'  => admin_url( 'admin.php?page=fabp-settings' ), // Liên kết đến trang cài đặt của plugin
        'meta'  => array(
            'class' => 'fabp-admin-bar',  // Class tùy chọn
            'title' => 'Cài đặt Multi-Contact' // Tooltip
        )
    );
    $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'fabp_admin_bar_menu', 100 );
