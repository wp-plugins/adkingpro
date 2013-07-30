<?php
class AdKingPro_Widget extends WP_Widget {

	public function __construct() {
            parent::__construct(
                    'adkingpro_widget', // Base ID
                    'AdKingPro', // Name
                    array( 'description' => __( 'Display an advert in the sidebar', 'text_domain' ), ) // Args
            );
	}

	public function widget( $args, $instance ) {
            extract( $args );
            $type = apply_filters( 'widget_type', $instance['type'] );
            $banner = apply_filters( 'widget_banner', $instance['banner'] );
            $code = 'type="'.$type.'"';
            if ($banner !== '') $code = 'banner="'.$banner.'"';
            if (function_exists('adkingpro_func')){ 
                echo do_shortcode('[adkingpro '.$code.']');
            }
	}

 	public function form( $instance ) {
            if ( isset( $instance[ 'type' ] ) ) {
                    $type = $instance[ 'type' ];
            }
            else {
                    $type = __( 'sidebar', 'text_domain' );
            }
            
            if ( isset( $instance[ 'banner' ] ) ) {
                    $banner = $instance[ 'banner' ];
            }
            else {
                    $banner = __( '', 'text_domain' );
            }
            ?>
            <p>
            <label for="<?php echo $this->get_field_name( 'type' ); ?>"><?php _e( 'Advert Type:' ); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
                <?php $types = get_terms(array('advert_types'));
                foreach ($types as $t) : ?>
                <option value='<?= $t->slug ?>'<?php if ($t->slug == esc_attr( $type )) echo ' selected'; ?>><?= $t->name ?></option>
                <?php endforeach ?>
            </select>
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'banner' ); ?>"><?php _e( 'Banner ID#:' ); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'banner' ); ?>" name="<?php echo $this->get_field_name( 'banner' ); ?>">
                <option value=''>Select Randomly</option>
                <?php $banners = get_posts(array('post_type'=>'adverts_posts'));
                foreach ($banners as $b) : ?>
                <option value='<?= $b->ID ?>'<?php if ($b->ID == esc_attr( $banner )) echo ' selected'; ?>><?= $b->ID ?></option>
                <?php endforeach; ?>
            </select>
            </p>
            <?php 
	}

	public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['type'] = ( !empty( $new_instance['type'] ) ) ? strip_tags( $new_instance['type'] ) : '';
            $instance['banner'] = ( !empty( $new_instance['banner'] ) ) ? strip_tags( $new_instance['banner'] ) : '';

            return $instance;
	}

}
?>