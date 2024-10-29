<?php
/* 
Plugin Name: Automatically add to cart for woocommerce 
Plugin URI: http://www.wordpress.org
Version: 1.0 
Author: Dominik Capkovic 
Description: Enables for each product automatically add to cart. */
// Save field for simple product 
add_action('woocommerce_process_product_meta', 'auto_add_cart_woo_backend_save');
// Create custom tab for backend 
add_action('woocommerce_product_write_panel_tabs', 'woo_add_custom_tab');
add_action('woocommerce_product_write_panels', 'auto_add_cart_woo_backend');
//Variable products 
add_action('woocommerce_product_after_variable_attributes', 'woo_auto_add_variable', 10, 2);
add_action('woocommerce_product_after_variable_attributes_js', 'woo_auto_add_variable_js');
add_action('woocommerce_process_product_meta_variable', 'save_woo_auto_add_variable', 10, 1);
//Loading languages from "languages" directory 
add_action('plugins_loaded', 'load_translation_auto_add_product');
function load_translation_auto_add_product()
{
    load_plugin_textdomain('autoadd2cart', false, basename(dirname(__FILE__)) . '/languages');
}
function woo_auto_add_variable($loop, $variation_data)
{
    echo '<tr>';
    woocommerce_wp_checkbox(array(
        'id' => '_auto_add_variable_checkbox[' . $loop . ']',
        'label' => __('Enable?', 'autoadd2cart'),
        'description' => __('Add  this variation automatically to cart after visit? ', 'autoadd2cart'),
        'value' => $variation_data['_auto_add_variable_checkbox'][0]
    ));
    echo '</tr><tr>';
    woocommerce_wp_text_input(array(
        'id' => '_auto_woo_number_field[' . $loop . ']',
        'label' => __('Quantity', 'autoadd2cart'),
        'desc_tip' => 'true',
        'description' => __('How many of this variation will be added', 'autoadd2cart'),
        'value' => $variation_data['_auto_woo_number_field'][0],
        'custom_attributes' => array(
            'step' => 'any',
            'min' => '0'
        )
    ));
    echo '</tr><tr>';
    woocommerce_wp_checkbox(array(
        'id' => '_auto_add_variable_checkbox_function2[' . $loop . ']',
        'label' => __('Disable?', 'autoadd2cart'),
        'description' => __('If disabled, plugin will add variation and set quantity regardless if product and set quantity is already in cart ', 'autoadd2cart'),
        'value' => $variation_data['_auto_add_variable_checkbox_function2'][0]
    ));
    echo '</tr>';
}
function woo_auto_add_variable_js()
{
    echo '<tr>';
    woocommerce_wp_checkbox(array(
        'id' => '_auto_add_variable_checkbox[ + loop + ]',
        'label' => __('Enable?', 'autoadd2cart'),
        'description' => __('Add this variation automatically to cart after visit?', 'autoadd2cart'),
        'value' => $variation_data['_auto_add_variable_checkbox'][0]
    ));
    echo '</tr><tr>';
    woocommerce_wp_text_input(array(
        'id' => '_auto_woo_number_field[ + loop + ]',
        'label' => __('Quantity', 'autoadd2cart'),
        'desc_tip' => 'true',
        'description' => __('How many of this variation will be added.', 'autoadd2cart'),
        'value' => $variation_data['_auto_woo_number_field'][0],
        'custom_attributes' => array(
            'step' => 'any',
            'min' => '0'
        )
    ));
    echo '</tr><tr>';
    woocommerce_wp_checkbox(array(
        'id' => '_auto_add_variable_checkbox_function2[ + $loop + ]',
        'label' => __('Disable?', 'autoadd2cart'),
        'description' => __('If disabled, plugin will add variation and set quantity regardless if product and set quantity is already in cart', 'autoadd2cart'),
        'value' => $variation_data['_auto_add_variable_checkbox_function2'][0]
    ));
    echo '</tr>';
}
function save_woo_auto_add_variable($post_id)
{
    if (isset($_POST['variable_sku'])):
        $variable_sku     = $_POST['variable_sku'];
        $variable_post_id = $_POST['variable_post_id'];
        $_checkbox        = $_POST['_auto_add_variable_checkbox'];
        for ($i = 0; $i < sizeof($variable_sku); $i++):
            $variation_id = (int) $variable_post_id[$i];
            if (isset($_checkbox[$i])) {
                update_post_meta($variation_id, '_auto_add_variable_checkbox', stripslashes($_checkbox[$i]));
            }
        endfor;
        $_number_field = $_POST['_auto_woo_number_field'];
        for ($i = 0; $i < sizeof($variable_sku); $i++):
            $variation_id = (int) $variable_post_id[$i];
            if (isset($_number_field[$i])) {
                update_post_meta($variation_id, '_auto_woo_number_field', stripslashes($_number_field[$i]));
            }
        endfor;
        $_checkbox2 = $_POST['_auto_add_variable_checkbox_function2'];
        for ($i = 0; $i < sizeof($variable_sku); $i++):
            $variation_id = (int) $variable_post_id[$i];
            if (isset($_checkbox[$i])) {
                update_post_meta($variation_id, '_auto_add_variable_checkbox_function2', stripslashes($_checkbox2[$i]));
            }
        endfor;
    endif;
}
function auto_add_cart_woo_backend()
{
    global $woocommerce, $post;
    if (function_exists('get_product')) {
        $product = get_product($post->ID);
        if ($product->is_type('simple')) {
            echo '<div id="data_auto_add" class="panel woocommerce_options_panel" style="display: none; "> <fieldset><p class="form-field">';
            woocommerce_wp_checkbox(array(
                'id' => '_checkbox_slider_woo_add',
                'wrapper_class' => '',
                'label' => __('Enable?', 'autoadd2cart'),
                'description' => __('Add automatically product to cart after visit? ', 'autoadd2cart')
            ));
            woocommerce_wp_text_input(array(
                'id' => '_auto_add_to_cart_quantity',
                'label' => __('Quantity', 'autoadd2cart'),
                'placeholder' => '',
                'description' => __('How many products will be added', 'autoadd2cart'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                )
            ));
            woocommerce_wp_checkbox(array(
                'id' => '_checkbox_slider_woo_add_function2',
                'wrapper_class' => '',
                'label' => __('Disable?', 'autoadd2cart'),
                'description' => __('If disabled, plugin will add product and set quantity regardless if product and set quantity is already in cart', 'autoadd2cart')
            ));
            echo '</p></fieldset>
<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=dominik%2ecapkovic%40gmail%2ecom&lc=US&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest">If you find this plugin useful, feel free to cheer me up :D . Thanks :) </a></p>
</div>';
        }
    }
}
function auto_add_cart_woo_backend_save($post_id)
{
    $woocommerce_checkbox = isset($_POST['_checkbox_slider_woo_add_function2']) ? 'yes' : 'no';
    update_post_meta($post_id, '_checkbox_slider_woo_add_function2', $woocommerce_checkbox);
    $woocommerce_quantity_field = $_POST['_auto_add_to_cart_quantity'];
    update_post_meta($post_id, '_auto_add_to_cart_quantity', $woocommerce_quantity_field);
    $woocommerce_checkbox1 = isset($_POST['_checkbox_slider_woo_add']) ? 'yes' : 'no';
    update_post_meta($post_id, '_checkbox_slider_woo_add', $woocommerce_checkbox1);
}
function woo_add_custom_tab()
{
    global $post;
    if (function_exists('get_product')) {
        $product = get_product($post->ID);
        if ($product->is_type('simple')) {
            echo '     <li class="custom_tab"><a href="#data_auto_add">' . __('Automatically add to cart after visit', 'autoadd2cart') . ' </a></li>';
        }
    }
}
//Now functionality 
add_action('wp', 'auto_add_product_to_cart_after_visit');
function auto_add_product_to_cart_after_visit()
{
if (!is_admin()) {
    if (is_product()) {
        global $post, $woocommerce;
        $product_id = $post->ID;
        $found      = false;
        if (function_exists('get_product')) {
            $product = get_product($post->ID);
            if ($product->is_type('simple')) {
                $cond_tag   = get_post_meta($product_id, '_checkbox_slider_woo_add', true);
                $quant      = get_post_meta($product_id, '_auto_add_to_cart_quantity', true);
                $cond_quant = get_post_meta($product_id, '_checkbox_slider_woo_add_function2', true);
                if (empty($quant)) {
                    $quant = 1;
                }
                if (!empty($cond_tag)) {
                    if (empty($cond_quant) || $cond_quant = 'no') {
                        //check if any product is already in cart         
                        if ($woocommerce->cart->cart_contents_count > 0 && $cond_tag = 'yes') {
                            foreach ($woocommerce->cart->get_cart_item_quantities() as $cart_item_key => $values_quant):
                                if ($cart_item_key == $product_id) {
                                    var_dump($values_quant);
                                    $found = true;
                                    if ($quant > $values_quant) {
                                        $quant       = $quant - $values_quant;
                                        $found_quant = true;
                                    }
                                }
                            endforeach;
                            // if product not found, add it  
                            if ($found_quant && $woocommerce->cart) {
                                $woocommerce->cart->add_to_cart($product_id, $quantity = $quant);
                            }
                            if (!$found && $woocommerce->cart)
                                $woocommerce->cart->add_to_cart($product_id, $quantity = $quant);
                        } else {
                            // if no products in cart, add it
                            if ($woocommerce->cart) {
                                $woocommerce->cart->add_to_cart($product_id, $quantity = $quant);
                            }
                        }
                    } else {
                        if ($woocommerce->cart) {
                            $woocommerce->cart->add_to_cart($product_id, $quantity = $quant);
                        }
                    }
                }
            }
            if ($product->is_type('variable')) {
                $variation_ids = $product->get_available_variations();
                if (is_array($variation_ids)) {
                    foreach ($variation_ids as $var_id):
                        $var_ide    = $var_id["variation_id"];
                        $cond_tag   = get_post_meta($var_ide, '_auto_add_variable_checkbox', true);
                        $quant      = get_post_meta($var_ide, '_auto_woo_number_field', true);
                        $cond_quant = get_post_meta($var_ide, '_auto_add_variable_checkbox_function2', true);
                        if (empty($quant)) {
                            $quant = 1;
                        }
                        if (!empty($cond_tag)) {
                            if (empty($cond_quant) || $cond_quant = 'no') {
                                //check if any product already in cart         
                                if ($woocommerce->cart->cart_contents_count > 0 && $cond_tag = 'yes') {
                                    foreach ($woocommerce->cart->get_cart_item_quantities() as $cart_item_key => $values_quant):
                                        if ($cart_item_key == $var_ide) {
                                            $found = true;
                                            if ($quant > $values_quant) {
                                                $quant       = $quant - $values_quant;
                                                $found_quant = true;
                                            }
                                        }
                                    endforeach;
                                    if ($found_quant && $woocommerce->cart) {
                                        $woocommerce->cart->add_to_cart($product_id, $quantity = $quant, $variation_id = $var_ide, $variation = $var_id['attributes']);
                                    }
                                    // if product with set variations not found, add it             
                                    if (!$found && $woocommerce->cart) {
                                        $woocommerce->cart->add_to_cart($product_id, $quantity = $quant, $variation_id = $var_ide, $variation = $var_id['attributes']);
                                    }
                                } else {
                                    // if no products in cart, add it 
                                    if ($woocommerce->cart) {
                                        $woocommerce->cart->add_to_cart($product_id, $quantity = $quant, $variation_id = $var_ide, $variation = $var_id['attributes']);
                                    }
                                }
                            } else {
                                if ($woocommerce->cart) {
                                    $woocommerce->cart->add_to_cart($product_id, $quantity = $quant, $variation_id = $var_ide, $variation = $var_id['attributes']);
                                }
                            }
                        }
                    endforeach;
                }
            }
        }
    }
}
}
?>