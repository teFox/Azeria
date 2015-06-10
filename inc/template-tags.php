<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package azeria
 */

if ( ! function_exists( 'the_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_posts_navigation() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'azeria' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( esc_html__( 'Older posts', 'azeria' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( esc_html__( 'Newer posts', 'azeria' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'the_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'azeria' ); ?></h2>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '%title' );
				next_post_link( '<div class="nav-next">%link</div>', '%title' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

/**
 * Show post author
 */
function azeria_post_author() {
	$author = sprintf(
		'<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);

	echo '<span class="entry-meta-item author"><i class="fa fa-user"></i> ' . $author . '</span>';
}

/**
 * Prints HTML with meta information for the current post-date.
 */
function azeria_post_date() {
	
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

	echo '<span class="entry-meta-item posted-on"><i class="fa fa-clock-o"></i> ' . $posted_on . '</span>'; // WPCS: XSS OK.

}

/**
 * Prints HTML with meta information for the current post-date.
 */
function azeria_post_comments() {
	
	if ( post_password_required() || ! comments_open() ) {
		return;
	}
	
	echo '<span class="entry-meta-item comments"><i class="fa fa-pencil-square-o"></i> ';
	comments_popup_link( esc_html__( 'Leave a comment', 'azeria' ), esc_html__( '1 Comment', 'azeria' ), esc_html__( '% Comments', 'azeria' ) );
	echo '</span>';

}

function azeria_post_categories() {

	// Hide category and tag text for pages.
	if ( 'post' != get_post_type() ) {
		return;
	}

	$categories_list = get_the_category_list( esc_html__( ', ', 'azeria' ) );
	if ( $categories_list && azeria_categorized_blog() ) {
		printf( '<span class="entry-meta-item cat-links"><i class="fa fa-folder-open"></i> ' . esc_html__( 'Posted in %1$s', 'azeria' ) . '</span>', $categories_list ); // WPCS: XSS OK.
	}

}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function azeria_post_tags() {
	// Hide category and tag text for pages.
	if ( 'post' != get_post_type() ) {
		return;
	}
		
	$tags_list = get_the_tag_list( '', esc_html__( ', ', 'azeria' ) );
	if ( $tags_list ) {
		printf( '<span class="entry-meta-item tags-links"><i class="fa fa-tags"></i> ' . esc_html__( 'Tagged %1$s', 'azeria' ) . '</span>', $tags_list ); // WPCS: XSS OK.
	}

}

if ( ! function_exists( 'the_archive_title' ) ) :
/**
 * Shim for `the_archive_title()`.
 *
 * Display the archive title based on the queried object.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( esc_html__( 'Category: %s', 'azeria' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( esc_html__( 'Tag: %s', 'azeria' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( esc_html__( 'Author: %s', 'azeria' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( esc_html__( 'Year: %s', 'azeria' ), get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'azeria' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( esc_html__( 'Month: %s', 'azeria' ), get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'azeria' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( esc_html__( 'Day: %s', 'azeria' ), get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'azeria' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = esc_html_x( 'Asides', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = esc_html_x( 'Galleries', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = esc_html_x( 'Images', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = esc_html_x( 'Videos', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = esc_html_x( 'Quotes', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = esc_html_x( 'Links', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = esc_html_x( 'Statuses', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = esc_html_x( 'Audio', 'post format archive title', 'azeria' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = esc_html_x( 'Chats', 'post format archive title', 'azeria' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( esc_html__( 'Archives: %s', 'azeria' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( esc_html__( '%1$s: %2$s', 'azeria' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = esc_html__( 'Archives', 'azeria' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @param string $title Archive title to be displayed.
	 */
	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;  // WPCS: XSS OK.
	}
}
endif;

if ( ! function_exists( 'the_archive_description' ) ) :
/**
 * Shim for `the_archive_description()`.
 *
 * Display category, tag, or term description.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		/**
		 * Filter the archive description.
		 *
		 * @see term_description()
		 *
		 * @param string $description Archive description to be displayed.
		 */
		echo $before . $description . $after;  // WPCS: XSS OK.
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function azeria_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'azeria_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'azeria_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so azeria_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so azeria_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in azeria_categorized_blog.
 */
function azeria_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'azeria_categories' );
}
add_action( 'edit_category', 'azeria_category_transient_flusher' );
add_action( 'save_post',     'azeria_category_transient_flusher' );

/**
 * Show site logo markup depending from site options
 */
function azeria_logo() {

	$logo_img = azeria_get_option( 'logo_img' );

	$logo_tag = 'h2';

	if ( is_front_page() ) {
		$logo_tag = 'h1';
	}

	if ( false != $logo_img ) {
		$logo_content = '<img src="' . esc_url( $logo_img ) . '" alt="' . get_bloginfo( 'name' ) . '">';
	} else {
		$logo_content = get_bloginfo( 'name' );
	}

	printf( '<%1$s class="site-logo"><a class="site-logo-link" href="%2$s">%3$s</a></%1$s>', $logo_tag, home_url( '/' ), $logo_content );

}

/**
 * Show posts listing content depending from options
 */
function azeria_blog_content() {

	$blog_content = azeria_get_option( 'blog_content', 'excerpt' );

	if ( 'excerpt' == $blog_content ) {
		the_excerpt();
		return;
	}

	/* translators: %s: Name of current post */
	the_content( sprintf(
		wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'azeria' ), array( 'span' => array( 'class' => array() ) ) ),
		the_title( '<span class="screen-reader-text">"', '"</span>', false )
	) );

	wp_link_pages( array(
		'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'azeria' ),
		'after'  => '</div>',
	) );

}

/**
 * Show format-related icon
 */
function azeria_format_icon( $format = 'standard' ) {

	$formats = array(
		'sticky'   => 'star',
		'standard' => 'pencil',
		'aside'    => 'map-marker',
		'image'    => 'picture-o',
		'gallery'  => 'picture-o',
		'video'    => 'video-camera',
		'quote'    => 'quote-left',
		'link'     => 'link'
	);

	if ( ! array_key_exists( $format, $formats ) ) {
		return '';
	}

	if ( is_sticky() ) {
		$format = 'sticky';
	}

	printf( '<div class="entry-icon"><i class="fa fa-%s"></i></div>', $formats[$format] );

}

/**
 * Show post meta data
 *
 * @param string $page     page, meta called from
 * @param string $position position, meta called from
 * @param string $disable  disabled meta keys array
 */
function azeria_post_meta( $page = 'loop', $position = 'header', $disable = array() ) {

	$default_meta = array(
		'author' => array(
			'page'     => $page,
			'position' => 'header',
			'callback' => 'azeria_post_author',
			'priority' => 1
		),
		'date' => array(
			'page'     => $page,
			'position' => 'header',
			'callback' => 'azeria_post_date',
			'priority' => 5
		),
		'comments' => array(
			'page'     => $page,
			'position' => 'header',
			'callback' => 'azeria_post_comments',
			'priority' => 5
		),
		'categories' => array(
			'page'     => 'single',
			'position' => 'footer',
			'callback' => 'azeria_post_categories',
			'priority' => 1
		),
		'tags' => array(
			'page'     => 'single',
			'position' => 'footer',
			'callback' => 'azeria_post_tags',
			'priority' => 5
		)
	);

	/**
	 * Get 3rd party meta items to show in meta block (or disable default from child theme)
	 */
	$meta_items = apply_filters( 'azeria_meta_items_data', $default_meta, $page, $position );
	$disable    = apply_filters( 'azeria_disabled_meta', $disable );

	foreach ( $meta_items as $meta_key => $data ) {

		if ( is_array( $disable ) && in_array( $meta_key, $disable ) ) {
			continue;
		}
		if ( empty( $data['page'] ) || $page != $data['page'] ) {
			continue;
		}
		if ( empty( $data['position'] ) || $position != $data['position'] ) {
			continue;
		}
		if ( empty( $data['callback'] ) || ! function_exists( $data['callback'] ) ) {
			continue;
		}

		$priority = ( ! empty( $data['priority'] ) ) ? absint( $data['priority'] ) : 10;

		add_action( 'azeria_post_meta_' . $page . '_' . $position, $data['callback'], $priority );
	}

	do_action( 'azeria_post_meta_' . $page . '_' . $position );

}

/**
 * Show post featured image
 * @param  boolean $is_linked liked image or not
 */
function azeria_post_thumbnail( $is_linked = true ) {

	if ( ! has_post_thumbnail() ) {
		return;
	}

	$is_enabled = true;

	if ( is_single() ) {
		$is_enabled = azeria_get_option( 'blog_single_image', true );
	} else {
		$is_enabled = azeria_get_option( 'blog_loop_image', true );
	}

	$is_enabled = (bool)$is_enabled;

	if ( ! $is_enabled ) {
		return;
	}

	if ( $is_linked ) {
		$format = '<figure class="entry-thumbnail"><a href="%2$s">%1$s<span class="link-marker"></span></a></figure>';
		$link   = get_permalink();
	} else {
		$format = '<figure class="entry-thumbnail">%1$s</figure>';
		$link   = false;
	}

	$image = get_the_post_thumbnail( get_the_id(), 'post-thumbnail', array( 'alt' => get_the_title() ) );

	printf( $format, $image, $link );

}

/**
 * Show read more button if enabled
 */
function azeria_read_more() {

	if ( post_password_required() ) {
		return;
	}

	$is_enabled = azeria_get_option( 'blog_more', true );

	if ( ! $is_enabled ) {
		return;
	}

	$text = azeria_get_option( 'blog_more_text', __( 'Read', 'azeria' ) );

	printf( '<div class="etry-more-btn"><a href="%1$s" class="button">%2$s</a></div>', get_permalink(), $text );

}

/**
 * Print options-related class to determine sidebar position
 */
function azeria_sidebar_class() {
	$sidebar_position = azeria_get_option( 'sidebar_position', 'right' );
	printf( '%s-sidebar', $sidebar_position );
}

/**
 * Show 'to top' button HTML markup
 */
function azeria_to_top() {

	echo apply_filters( 
		'azeria_to_top_button',
		'<div id="back-top" class="back-top-btn"><a href="#"><i class="fa fa-angle-up"></i></a></div>'
	);

}