<?php
/**
 * Class Name: Social_Links
 * Description: A custom social links menu.
 * Version: 1.0.0
 */
class Custom_Social_Links extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'custom_social_links',
			__( 'Social Links', 'child-theme' ),
			[ 'classname' => 'social-links', 'description' => __( 'Displays social media links.', 'child-theme' ), ]
		);
	}

	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$social_links = get_social_links_array();
		$hash_text = get_theme_mod( 'hash_text' );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) { echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $title ) ) . $args['after_title']; }
		echo '<div class="wrap">';

			foreach ( $social_links as $social_link ) {
				$url = get_theme_mod( $social_link . '_link' );
				if ( empty( $url ) ) { continue; }
				$aria_label = sprintf( '%s %s', esc_attr( ucfirst( $social_link ) ), esc_html__( '(New Window)', 'child-theme' ) );

				echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . $aria_label . '">';
					// If hash is custom text
					if ( $social_link === 'hash' && ! empty( $hash_text ) ) {
						echo esc_html( $hash_text );
					} else {
						echo render_template( 'icons/' . sanitize_file_name( $social_link ) . '.php' );
					}
				echo '</a>';
			}

		echo '</div>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'child-theme' ); ?></label>
				<input  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		return [ 'title' => ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '', ];
	}

}