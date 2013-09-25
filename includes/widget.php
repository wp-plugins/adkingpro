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
            $render = apply_filters( 'widget_render', $instance['render'] );
            $rotate = apply_filters( 'widget_rotate', $instance['rotate'] );
            $effect = apply_filters( 'widget_effect', $instance['effect'] );
            $speed = apply_filters( 'widget_speed', $instance['speed'] );
            $changespeed = apply_filters( 'widget_changespeed', $instance['changespeed'] );
            $code = 'type="'.$type.'"';
            if ($banner !== '') $code = 'banner="'.$banner.'"';
            if ($render !== '' && is_numeric($render)) $code .= ' render="'.$render.'"';
            if ($rotate == 'true') $code .= ' rotate="true"';
            if ($rotate == 'true' && $effect !== '') $code .= ' effect="'.$effect.'"';
            if ($rotate == 'true' && $speed !== '' && is_numeric($speed)) $code .= ' speed="'.$speed.'"';
            if ($rotate == 'true' && $changespeed !== '' && is_numeric($changespeed)) $code .= ' changespeed="'.$changespeed.'"';
            if (function_exists('adkingpro_func')){ 
                echo do_shortcode('[adkingpro '.$code.']');
            }
	}

 	public function form( $instance ) {
            if ( isset( $instance[ 'type' ] ) ) { $type = $instance[ 'type' ]; }
            else { $type = __( 'sidebar', 'text_domain' ); }
            
            if ( isset( $instance[ 'banner' ] ) ) { $banner = $instance[ 'banner' ]; }
            else { $banner = __( '', 'text_domain' ); }
            
            if ( isset( $instance[ 'render' ] ) ) { $render = $instance[ 'render' ]; }
            else { $render = __( '', 'text_domain' ); }
            
            if ( isset( $instance[ 'rotate' ] ) ) { $rotate = $instance[ 'rotate' ]; }
            else { $rotate = __( '', 'text_domain' ); }
            
            if ( isset( $instance[ 'effect' ] ) ) { $effect = $instance[ 'effect' ]; }
            else { $effect = __( '', 'text_domain' ); }
            
            if ( isset( $instance[ 'speed' ] ) ) { $speed = $instance[ 'speed' ]; }
            else { $speed = __( '', 'text_domain' ); }
            
            if ( isset( $instance[ 'changespeed' ] ) ) { $changespeed = $instance[ 'changespeed' ]; }
            else { $changespeed = __( '', 'text_domain' ); }
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
            <p>
            <label for="<?php echo $this->get_field_name( 'render' ); ?>"><?php _e( '# to render:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'render' ); ?>" name="<?php echo $this->get_field_name( 'render' ); ?>" value="<?= esc_attr( $render ) ?>">
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'rotate' ); ?>"><?php _e( 'Auto Rotation:' ); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'rotate' ); ?>" name="<?php echo $this->get_field_name( 'rotate' ); ?>" onChange="if (jQuery(this).val() == 'true'){ jQuery(this).parents('form').find('.akp_slideshow_options').fadeIn();}else{jQuery(this).parents('form').find('.akp_slideshow_options').fadeOut();}">
                <option value='false'<?php if ('false' == esc_attr( $rotate )) echo ' selected'; ?>>Off</option>
                <option value='true'<?php if ('true' == esc_attr( $rotate )) echo ' selected'; ?>>On</option>
            </select>
            </p>
            <div class="akp_slideshow_options" style="display: <?php if ($rotate == "true") echo 'block'; else echo 'none'; ?>;">
                <p>
                <label for="<?php echo $this->get_field_name( 'effect' ); ?>"><?php _e( 'Rotation Effect:' ); ?></label> 
                <select class="widefat" id="<?php echo $this->get_field_id( 'effect' ); ?>" name="<?php echo $this->get_field_name( 'effect' ); ?>">
                    <option value='fade'<?php if ('fade' == esc_attr( $effect )) echo ' selected'; ?>>Fade</option>
                    <option value='slideLeft'<?php if ('slideLeft' == esc_attr( $effect )) echo ' selected'; ?>>Slide Left</option>
                    <option value='none'<?php if ('none' == esc_attr( $effect )) echo ' selected'; ?>>None</option>
                </select>
                </p>
                <p>
                <label for="<?php echo $this->get_field_name( 'speed' ); ?>"><?php _e( 'Pause Speed:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>" value="<?= esc_attr( $speed ) ?>">
                </p>
                <p>
                <label for="<?php echo $this->get_field_name( 'changespeed' ); ?>"><?php _e( 'Change Speed:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'changespeed' ); ?>" name="<?php echo $this->get_field_name( 'changespeed' ); ?>" value="<?= esc_attr( $changespeed ) ?>">
                </p>
            </div>
            <?php 
	}

	public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['type'] = ( !empty( $new_instance['type'] ) ) ? strip_tags( $new_instance['type'] ) : '';
            $instance['banner'] = ( !empty( $new_instance['banner'] ) ) ? strip_tags( $new_instance['banner'] ) : '';
            $instance['rotate'] = ( !empty( $new_instance['rotate'] ) ) ? strip_tags( $new_instance['rotate'] ) : '';
            $instance['speed'] = ( !empty( $new_instance['speed'] ) ) ? strip_tags( $new_instance['speed'] ) : '';
            $instance['changespeed'] = ( !empty( $new_instance['changespeed'] ) ) ? strip_tags( $new_instance['changespeed'] ) : '';
            $instance['effect'] = ( !empty( $new_instance['effect'] ) ) ? strip_tags( $new_instance['effect'] ) : '';
            $instance['render'] = ( !empty( $new_instance['render'] ) ) ? strip_tags( $new_instance['render'] ) : '';
            
            return $instance;
	}

}
?>