<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $element_fields = Woolentor_Admin_Fields::instance()->fields()['woolentor_sales_notification_tabs'];
    $element_keys = array_column( $element_fields, 'name' );

?>
<div id="woolentor_sales_notification_tabs" class="woolentor-admin-main-tab-pane">
    <div class="woolentor-admin-main-tab-pane-inner">
        <form class="woolentor-dashboard" id="woolentor-dashboard-sales-notification-form" action="#" method="post" data-section="woolentor_sales_notification_tabs" data-fields='<?php echo wp_json_encode( $element_keys ); ?>'>
            <div class="woolentor-admin-options">

                <?php
                    foreach( $element_fields as $key => $field ){
                        Woolentor_Admin_Fields_Manager::instance()->add_field( $field, 'woolentor_sales_notification_tabs' );
                    }
                ?>

                <div class="woolentor-admin-option">
                    <button class="woolentor-admin-btn-save woolentor-admin-btn woolentor-admin-btn-primary hover-effect-1" style="margin-left: auto;" disabled="disabled"><?php echo esc_html__('Save Changes','woolentor');?></button>
                </div>

            </div>
        </form>
    </div>
</div>