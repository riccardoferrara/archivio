<<?php echo esc_attr( $title_tag ); ?> <?php qode_framework_class_attribute( $title_classes ); ?> <?php qode_framework_inline_style( $title_styles ); ?>>
    <?php echo qode_framework_wp_kses_html( 'content', $title ); ?>
</<?php echo esc_attr( $title_tag ); ?>>
