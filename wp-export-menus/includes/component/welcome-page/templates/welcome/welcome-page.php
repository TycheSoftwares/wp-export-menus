<?php
/**
 * Welcome page on activate or updation of the plugin
 */

$wem_array = get_query_var( 'wem_array' );

$badge_url = $wem_array['badge_url'];

$ts_dir_image_path = $wem_array['ts_dir_image_path'];

$ts_plugin_name = $wem_array['plugin_name'];

?>
<style>
    .feature-section .feature-section-item {
        float:left;
        width:48%;
    }
</style>
<div class="wrap about-wrap">

    <?php echo $wem_array[ 'get_welcome_header'] ?>

    <div style="float:left;width: 80%;">
    <p class="about-text" style="margin-right:20px;"><?php
        printf(
            __( "Thank you for activating or updating to the latest version of $ts_plugin_name! If you're a first time user, welcome! You're well to Export your WordPress menus and import them to any of your other websites." )
        );
        ?></p>
    </div>
    <div class="faq-badge"><img src="<?php echo $badge_url; ?>" style="width:150px;"/></div>

    <p>&nbsp;</p>

    <div class="feature-section clearfix introduction">

        <h3><?php esc_html_e( "Get Started with $ts_plugin_name", 'wem' ); ?></h3>

        <div class="video feature-section-item" style="float:left;padding-right:10px;">
            <img src="<?php echo $ts_dir_image_path . 'export-wordpress-menus.png' ?>"
                    alt="<?php esc_attr_e( $ts_plugin_name, 'wem' ); ?>" style="width:600px;">
        </div>

        <div class="content feature-section last-feature">
            <h3><?php esc_html_e( 'Export WordPress menus', 'wem' ); ?></h3>

            <p><?php esc_html_e( 'To export menus select Navigation Menu Items option under Tools -> Export menu. After selecting this option if you want to export menus only for particular months then select the Start date and End date option. And click on Download Export File button.', 'wem' ); ?></p>
            <a href="export.php" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Click here to Export Menu', 'wem' ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
    </div>

    <div class="feature-section clearfix">

        <div class="content feature-section-item">

            <h3><?php esc_html_e( 'Getting to Know Tyche Softwares', 'acs' ); ?></h3>

            <ul class="ul-disc">
                <li><a href="https://tychesoftwares.com/?utm_source=wpaboutp    age&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank"><?php esc_html_e( 'Visit the Tyche Softwares Website', 'acs' ); ?></a></li>
                <li><a href="https://tychesoftwares.com/premium-woocommerce-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank"><?php esc_html_e( 'View all Premium Plugins', 'acs' ); ?></a>
                <ul class="ul-disc">
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank">Abandoned Cart Pro Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank">Booking & Appointment Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank">Order Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank">Product Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank">Deposits for WooCommerce</a></li>
                </ul>
                </li>
                <li><a href="https://tychesoftwares.com/about/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=WpContentCopyPlugin" target="_blank"><?php esc_html_e( 'Meet the team', 'acs' ); ?></a></li>
            </ul>
        </div>
    </div>            
    <!-- /.feature-section -->
</div>
