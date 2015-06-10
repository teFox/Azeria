<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package azeria
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function azeria_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'azeria_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function azeria_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'azeria' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'azeria_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function azeria_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'azeria_render_title' );
endif;

/**
 * Get allowed socials data (to add options into customizer and output on front)
 */
function azeria_allowed_socials() {

	return apply_filters(
		'azeria_allowed_socials',
		array(
			'facebook' => array(
				'label'   => __( 'Facebook', 'azeria' ),
				'icon'    => 'fa fa-facebook',
				'default' => 'https://www.facebook.com/'
			),
			'twitter' => array(
				'label'   => __( 'Twitter', 'azeria' ),
				'icon'    => 'fa fa-twitter',
				'default' => 'https://twitter.com/'
			),
			'google-plus' => array(
				'label'   => __( 'Google +', 'azeria' ),
				'icon'    => 'fa fa-google-plus',
				'default' => 'https://plus.google.com/'
			),
			'instagram' => array(
				'label'   => __( 'Instagram', 'azeria' ),
				'icon'    => 'fa fa-instagram',
				'default' => 'https://instagram.com/'
			),
			'pinterest' => array(
				'label'   => __( 'Pinterest', 'azeria' ),
				'icon'    => 'fa fa-pinterest',
				'default' => 'https://www.pinterest.com/'
			),
			'dribbble' => array(
				'label'   => __( 'Dribbble', 'azeria' ),
				'icon'    => 'fa fa-dribbble',
				'default' => 'https://dribbble.com/'
			)
		)
	);

}

/**
 * Custom comment output
 */
function azeria_comment( $comment, $args, $depth ) {

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'photolab' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'photolab' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<div class="comment-author-thumb">
				<?php echo get_avatar( $comment, 50 ); ?>
			</div><!-- .comment-author -->
			<div class="comment-content">
				<div class="comment-meta">
					<?php printf( '<div class="comment-author">%s</div>', get_comment_author_link() ); ?>
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php echo human_time_diff( get_comment_time('U'), current_time('timestamp') ) . ' ' . __( 'ago', 'photolab' ); ?>
					</time>
					<?php
						comment_reply_link( 
							array_merge( $args, array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<div class="reply">',
								'after'     => '</div>',
							) ),
							$comment
						);
					?>
				</div>
				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'photolab' ); ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->
		</article><!-- .comment-body -->

	<?php
	endif;

}