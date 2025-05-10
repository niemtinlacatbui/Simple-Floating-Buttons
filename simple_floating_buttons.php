<?php
/*
Plugin Name: Simple Floating Buttons
Plugin URI: https://www.linkedin.com/in/binhnn/
Description: WordPress Plugin to Add Floating Contact and Back to Top Buttons Simply and Effectively
Version: 1.2
Author: Binhnn
Author URI: https://binhnn.dev/
*/

/**
 * 1. Cài đặt các giá trị mặc định khi kích hoạt plugin
 */
function binhnn_default_options() {
    $default_options = array(
        'enable_social'   => '0', // Default disabled
        'zalo_link'      => '#',
        'phone_link'     => '#',
        'messenger_link' => '#',
        'email_link'     => '#',
        'custom_socials' => array(), // Mảng lưu các social tùy chỉnh
        'back_to_top'    => '0', // Default disabled
        'back_to_top_position' => 'right',
    );
    if ( ! get_option('binhnn_options') ) {
        add_option('binhnn_options', $default_options);
    }
}
register_activation_hook(__FILE__, 'binhnn_default_options');

/**
 * 2. Thêm menu cài đặt vào Dashboard
 */
function binhnn_add_menu() {
    add_menu_page(
        'Simple Floating Buttons',  // Tiêu đề trang
        'Simple Floating Buttons',  // Tên menu
        'manage_options',           // Quyền truy cập
        'binhnn-settings',          // Slug menu
        'binhnn_settings_page',     // Hàm hiển thị trang cài đặt
        'dashicons-admin-generic',  // Icon
        81                          // Vị trí menu
    );
}
add_action('admin_menu', 'binhnn_add_menu');

/**
 * 3. Xoá mục menu của plugin khỏi thanh bên trái.
 */
function binhnn_remove_admin_menu_item() {
    remove_menu_page('binhnn-settings');
}
add_action('admin_menu', 'binhnn_remove_admin_menu_item', 999);

/**
 * 4. Hàm hiển thị trang cài đặt
 */
function binhnn_settings_page() {
    if ( ! current_user_can('manage_options') ) {
        wp_die(__('Bạn không có quyền truy cập trang này.'));
    }
    
    // Lấy các giá trị đang lưu
    $options = get_option('binhnn_options');
    
    // Danh sách social có thể thêm
    $available_socials = array(
        'facebook'  => 'Facebook',
        'viber'     => 'Viber',
        'whatsapp'  => 'WhatsApp',
        'telegram'  => 'Telegram',
        'instagram' => 'Instagram',
    );
    
    // Xử lý lưu dữ liệu khi form được submit
    if ( isset($_POST['binhnn_submit']) ) {
        check_admin_referer('binhnn_settings_verify');
        
        $options['enable_social']   = isset($_POST['enable_social']) ? '1' : '0';
        $options['zalo_link']      = sanitize_text_field($_POST['zalo_link']);
        $options['phone_link']     = sanitize_text_field($_POST['phone_link']);
        $options['messenger_link'] = sanitize_text_field($_POST['messenger_link']);
        $options['email_link']     = sanitize_text_field($_POST['email_link']);
        $options['back_to_top']    = isset($_POST['back_to_top']) ? '1' : '0';
        $options['back_to_top_position'] = sanitize_text_field($_POST['back_to_top_position']);
        
        // Xử lý custom socials
        $custom_socials = array();
        if ( isset($_POST['custom_socials']) && is_array($_POST['custom_socials']) ) {
            foreach ( $_POST['custom_socials'] as $social => $link ) {
                if ( !empty($link) && $link !== '#' && array_key_exists($social, $available_socials) ) {
                    $custom_socials[$social] = sanitize_text_field($link);
                }
            }
        }
        $options['custom_socials'] = $custom_socials;
        
        update_option('binhnn_options', $options);
        
        echo '<div class="updated"><p>Cập nhật thành công!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Simple Floating Buttons Settings</h1>
        
        <form method="post" action="" id="binhnn-settings-form">
            <?php wp_nonce_field('binhnn_settings_verify'); ?>
            
            <h2>Social Links Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Social Links</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_social" value="1" <?php checked('1', $options['enable_social']); ?> />
                            Enable Social Links buttons
                        </label>
                    </td>
                </tr>
                <tr valign="top" class="social-options" style="<?php echo $options['enable_social'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                    <th scope="row">Phone Link</th>
                    <td>
                        <input type="text" name="phone_link" value="<?php echo esc_attr($options['phone_link']); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top" class="social-options" style="<?php echo $options['enable_social'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                    <th scope="row">Email Link</th>
                    <td>
                        <input type="text" name="email_link" value="<?php echo esc_attr($options['email_link']); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top" class="social-options" style="<?php echo $options['enable_social'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                    <th scope="row">Zalo Link</th>
                    <td>
                        <input type="text" name="zalo_link" value="<?php echo esc_attr($options['zalo_link']); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top" class="social-options" style="<?php echo $options['enable_social'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                    <th scope="row">Messenger Link</th>
                    <td>
                        <input type="text" name="messenger_link" value="<?php echo esc_attr($options['messenger_link']); ?>" class="regular-text" />
                    </td>
                </tr>

                <!-- Hiển thị custom socials đã thêm -->
                <tbody id="custom-socials-list">
                    <?php if ( !empty($options['custom_socials']) ) : ?>
                        <?php foreach ( $options['custom_socials'] as $social => $link ) : ?>
                            <tr valign="top" class="social-options custom-social-row" style="<?php echo $options['enable_social'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                                <th scope="row"><?php echo esc_html($available_socials[$social]); ?> Link</th>
                                <td>
                                    <input type="text" name="custom_socials[<?php echo esc_attr($social); ?>]" value="<?php echo esc_attr($link); ?>" class="regular-text" />
                                    <button type="button" class="button button-secondary remove-social" data-social="<?php echo esc_attr($social); ?>">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Thêm social mới -->
            <h3>Thêm Social Khác</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Chọn Social</th>
                    <td>
                        <select id="new-social">
                            <option value="">-- Chọn Social --</option>
                            <?php foreach ( $available_socials as $key => $name ) : ?>
                                <?php if ( !isset($options['custom_socials'][$key]) ) : ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" id="add-social" class="button button-primary">Thêm</button>
                    </td>
                </tr>
            </table>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

            <h2>Back to Top Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Back to Top</th>
                    <td>
                        <label>
                            <input type="checkbox" name="back_to_top" value="1" <?php checked('1', $options['back_to_top']); ?> />
                            Enable Back to Top button
                        </label>
                    </td>
                </tr>
                <tr valign="top" class="backtotop-options" style="<?php echo $options['back_to_top'] == '0' ? 'opacity: 0.5;' : ''; ?>">
                    <th scope="row">Button Position</th>
                    <td>
                        <select name="back_to_top_position">
                            <option value="right" <?php selected($options['back_to_top_position'], 'right'); ?>>Right</option>
                            <option value="left" <?php selected($options['back_to_top_position'], 'left'); ?>>Left</option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Lưu thay đổi', 'primary', 'binhnn_submit'); ?>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

        <h2>Thông tin Plugin Simple Floating Buttons</h2>
        <p><strong>Version:</strong> 1.2</p>
        <p><strong>Tác giả:</strong> Binhnn</p>
        <p><strong>Mail:</strong> <a href="mailto:contact@binhnn.dev">contact@binhnn.dev</a></p>
        <p><strong>Phone:</strong> <a href="tel:0876011134">0876011134</a></p>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Social Links toggle
        $('input[name="enable_social"]').change(function() {
            if($(this).is(':checked')) {
                $('.social-options').css('opacity', '1');
            } else {
                $('.social-options').css('opacity', '0.5');
            }
        });

        // Back to Top toggle
        $('input[name="back_to_top"]').change(function() {
            if($(this).is(':checked')) {
                $('.backtotop-options').css('opacity', '1');
            } else {
                $('.backtotop-options').css('opacity', '0.5');
            }
        });

        // Thêm social mới
        $('#add-social').on('click', function() {
            var newSocial = $('#new-social').val();
            if (!newSocial) {
                alert('Vui lòng chọn một social để thêm!');
                return;
            }

            var socialName = $('#new-social option[value="' + newSocial + '"]').text();
            var isEnabled = $('input[name="enable_social"]').is(':checked') ? '' : 'opacity: 0.5;';

            // Thêm hàng mới vào danh sách
            var rowHtml = '<tr valign="top" class="social-options custom-social-row" style="' + isEnabled + '">' +
                          '<th scope="row">' + socialName + ' Link</th>' +
                          '<td>' +
                          '<input type="text" name="custom_socials[' + newSocial + ']" value="#" class="regular-text" />' +
                          '<button type="button" class="button button-secondary remove-social" data-social="' + newSocial + '">Xóa</button>' +
                          '</td>' +
                          '</tr>';
            $('#custom-socials-list').append(rowHtml);

            // Xóa tùy chọn khỏi dropdown
            $('#new-social option[value="' + newSocial + '"]').remove();
            $('#new-social').val('');
        });

        // Xóa social
        $(document).on('click', '.remove-social', function() {
            var social = $(this).data('social');
            var socialName = $(this).closest('tr').find('th').text().replace(' Link', '');

            // Xóa hàng
            $(this).closest('tr').remove();

            // Thêm lại tùy chọn vào dropdown
            $('#new-social').append('<option value="' + social + '">' + socialName + '</option>');
        });
    });
    </script>
    <?php
}

/**
 * 5. Hàm chèn HTML Simple Floating Buttons vào trang (sử dụng hook wp_footer)
 */
function binhnn_display_float_action_button() {
    $options = get_option('binhnn_options');
    
    if ($options['enable_social'] == '1') {
        // Đếm số nút sẽ hiển thị để tránh hiển thị nút trắng
        $active_buttons = 0;
        $buttons_html = '';

        // 4 social chính
        if (!empty($options['phone_link']) && $options['phone_link'] !== '#') {
            $buttons_html .= '<a href="' . esc_url($options['phone_link']) . '" class="buttons phone" title="Phone"><i class="fas fa-phone"></i></a>';
            $active_buttons++;
        }
        if (!empty($options['email_link']) && $options['email_link'] !== '#') {
            $buttons_html .= '<a href="' . esc_url($options['email_link']) . '" class="buttons email" title="Email"><i class="fas fa-envelope"></i></a>';
            $active_buttons++;
        }
        if (!empty($options['zalo_link']) && $options['zalo_link'] !== '#') {
            $buttons_html .= '<a href="' . esc_url($options['zalo_link']) . '" class="buttons zalo" title="Zalo"><i class="fab fa-zalo"></i></a>';
            $active_buttons++;
        }
        if (!empty($options['messenger_link']) && $options['messenger_link'] !== '#') {
            $buttons_html .= '<a href="' . esc_url($options['messenger_link']) . '" class="buttons messenger" title="Messenger"><i class="fab fa-facebook-messenger"></i></a>';
            $active_buttons++;
        }

        // Social tùy chỉnh
        if (!empty($options['custom_socials'])) {
            foreach ($options['custom_socials'] as $social => $link) {
                if (!empty($link) && $link !== '#') {
                    $icon_class = '';
                    switch ($social) {
                        case 'facebook':
                            $icon_class = 'fab fa-facebook-f';
                            break;
                        case 'viber':
                            $icon_class = 'fab fa-viber';
                            break;
                        case 'whatsapp':
                            $icon_class = 'fab fa-whatsapp';
                            break;
                        case 'telegram':
                            $icon_class = 'fab fa-telegram-plane';
                            break;
                        case 'instagram':
                            $icon_class = 'fab fa-instagram';
                            break;
                    }
                    $buttons_html .= '<a href="' . esc_url($link) . '" class="buttons ' . esc_attr($social) . '" title="' . esc_attr(ucfirst($social)) . '"><i class="' . esc_attr($icon_class) . '"></i></a>';
                    $active_buttons++;
                }
            }
        }

        // Chỉ hiển thị nếu có ít nhất 1 nút được kích hoạt
        if ($active_buttons > 0) {
        ?>
        <nav class="float-action-button" style="z-index:9999; position: fixed; bottom: 60px; right: 5px; margin: 1em;">
            <?php echo $buttons_html; ?>
            <a href="#" class="buttons main-button">
                <i class="fa fa-times"></i>
                <i class="fa fa-share-alt"></i>
            </a>
        </nav>
        <?php
        }
    }

    if ($options['back_to_top'] == '1') {
    ?>
    <!-- Back to Top Button -->
    <div class="progress-wrap" style="<?php echo $options['back_to_top_position'] == 'left' ? 'right: auto; left: 30px;' : 'right: 30px; left: auto;'; ?>">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
        </svg>
    </div>
    <?php
    }
}
add_action('wp_footer', 'binhnn_display_float_action_button');

/**
 * 6. Enqueue CSS và JS
 */
function binhnn_enqueue_scripts() {
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');
    
    // Enqueue plugin styles
    wp_enqueue_style('binhnn-style', plugin_dir_url(__FILE__) . 'css/binhnn-style.css', array(), '1.1');
    
    // Add back to top script inline
    $back_to_top_script = "
        (function($) {
            'use strict';
            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded. Back to Top script will not run.');
                return;
            }

            $(document).ready(function(){
                console.log('Back to Top script loaded.');

                var progressPath = document.querySelector('.progress-wrap path');
                if (!progressPath) {
                    console.error('Progress path not found. Ensure Back to Top element exists in the DOM.');
                    return;
                }

                var pathLength = progressPath.getTotalLength();
                progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
                progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
                progressPath.style.strokeDashoffset = pathLength;
                progressPath.getBoundingClientRect();
                progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';    
                
                var updateProgress = function () {
                    var scroll = $(window).scrollTop();
                    var height = $(document).height() - $(window).height();
                    var progress = pathLength - (scroll * pathLength / height);
                    progressPath.style.strokeDashoffset = progress;
                };
                
                updateProgress();
                $(window).scroll(updateProgress); 
                
                var offset = 50;
                var duration = 550;
                
                $(window).on('scroll', function() {
                    if ($(this).scrollTop() > offset) {
                        $('.progress-wrap').addClass('active-progress');
                    } else {
                        $('.progress-wrap').removeClass('active-progress');
                    }
                });       
                
                $('.progress-wrap').on('click', function(event) {
                    event.preventDefault();
                    $('html, body').animate({scrollTop: 0}, duration);
                    return false;
                });
            });
        })(jQuery);
    ";
    wp_add_inline_script('jquery', $back_to_top_script);
}
add_action('wp_enqueue_scripts', 'binhnn_enqueue_scripts');

// Thêm mục menu vào thanh Admin Bar
function binhnn_admin_bar_menu( $wp_admin_bar ) {
    if ( ! current_user_can('manage_options') ) {
        return;
    }
    $args = array(
        'id'    => 'binhnn-settings',
        'title' => 'Simple Floating Buttons',
        'href'  => admin_url('admin.php?page=binhnn-settings'),
        'meta'  => array(
            'class' => 'binhnn-admin-bar',
            'title' => 'Cài đặt Simple Floating Buttons'
        )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'binhnn_admin_bar_menu', 100);
?>