<?php
/** Get Animations **/
if(!function_exists('timeline_animate_class')) {
	function timeline_animate_class($addon_animate,$effect,$delay) {
		if($addon_animate == 'on') : 
			wp_enqueue_script( 'appear' );			
			wp_enqueue_script( 'animate' );		
			$animate_class = ' animate-in" data-anim-type="'.$effect.'" data-anim-delay="'.$delay.'"'; 
		else :
			$animate_class = '"';
		endif;		
		return $animate_class;
	}
}

/** Get Category **/
if(!function_exists('timeline_get_category')) {
	function timeline_get_category($source,$posts_type,$css_link,$limit = 1) {
		$separator = ' ';
		$output = '';	
		$count = 1;
		if($source=='wp_posts') {			
			$categories = get_the_category();
			if($categories){
				foreach($categories as $category) {
					$output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",'elementor-blog-layouts' ), $category->name ) ) . '" '.$css_link.'>'.esc_html($category->cat_name).'</a>'.esc_html($separator);
					if($count == $limit) { break; }
					$count++;
				}
			}
		} elseif($source=='post_type') {
			global $post;
			$taxonomy_names = get_object_taxonomies( $posts_type );
			$term_list = wp_get_post_terms($post->ID,$taxonomy_names);
			if($term_list){
				foreach ($term_list as $tax_term) {
					$output .= '<a href="' . esc_attr(get_term_link($tax_term, $posts_type)) . '" title="' . sprintf( __( "View all posts in %s",'elementor-blog-layouts' ), $tax_term->name ) . '" '.$css_link.'>' . esc_html($tax_term->name) .'</a>'.esc_html($separator);
				}
			}
		}
		$return = trim($output, $separator);
		return $return;
	}
}

/** Get Thumbnails **/
if(!function_exists('timeline_get_thumb')) {
	function timeline_get_thumb($thumbs_size = 'bloglayouts-normal') {
		global $post;
		$link = get_the_permalink();
		if(has_post_thumbnail()){ 
				$id_post = get_the_id();					
				$single_image = wp_get_attachment_image_src( get_post_thumbnail_id($id_post), $thumbs_size );	
				$return = '<a href="'.$link.'"><img class="bloglayouts-thumbs" src="'.$single_image[0].'" alt="'.get_the_title().'"></a>';
			} else {               
				$return = '';                 
		}
		return $return;
	}
}

/** Check Post Format **/
if(!function_exists('bloglayouts_check_post_format')) {
	function timeline_check_post_format() {
		global $post;
		$format = get_post_format_string( get_post_format() );
		echo $format;
		if($format == 'Video') :
			$return = '<span class="fa fa-play"></span>';
		elseif($format == 'Audio') :
			$return = '<span class="fa fa-headphones"></span>';
		elseif($format == 'Gallery') :
			$return = '<span class="fa fa-images"></span>';			
		else :
			$return = '<span class="fa fa-image"></span>';
		endif;
		return $return;
	}
}

/** Get News Excerpt **/
if(!function_exists('timeline_get_news_excerpt')) {
	function timeline_get_news_excerpt($excerpt = 'default',$readmore = 'on',$css_link) {
		global $post;
		if($excerpt == 'default') : 
			$return = get_the_excerpt();
		else :
			$return = substr(get_the_excerpt(), 0, $excerpt);
			if($readmore == 'on') :
				$return .= '<a class="article-read-more" href="'. get_permalink($post->ID) . '" '.$css_link.'><i class="fa fa-angle-double-right"></i></a>';
			else :
				$return .= '...';
			endif;
		endif;
		return $return;
	}
}

/** Get Author **/
if(!function_exists('timeline_get_author')) {
	function timeline_get_author($css_link) {
		$return = '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'" '.$css_link.'>'.get_the_author_meta( 'display_name' ).'</a>';
		return $return;
	}	
}

if(!function_exists('timeline_query')) {
	function timeline_query( $source,
						$posts_source, 
						$posts_type, 
						$categories,
						$categories_post_type,
						$order, 
						$orderby, 
						$pagination, 
						$pagination_type,
						$num_posts, 
						$num_posts_page) {
								  
						if($orderby == 'views') { 
								$orderby = 'meta_value_num'; 
								$view_order = 'views';
						} else { $view_order = ''; }	
										
						if($source == 'post_type') {
							$posts_source = 'all_posts';
						}
						
						if($posts_source == 'all_posts') {
						
							$query = 'post_type=Post&post_status=publish&ignore_sticky_posts=1&orderby='.$orderby.'&order='.$order.'';						
							
							// CUSTOM POST TYPE
							if($source == 'post_type') {
								$query .= '&post_type='.$posts_type.'';
							}

							if($view_order == 'views') { 
								$query .= '&meta_key=wpb_post_views_count';
							}
							
							// CATEGORIES POST TYPE
							if($categories_post_type != '' && !empty($categories_post_type) && $source == 'post_type') {
								$taxonomy_names = get_object_taxonomies( $posts_type );
								$query .= '&'.$taxonomy_names[0].'='.$categories_post_type.'';	
							}

							// CATEGORIES POSTS
							if($categories != '' && $categories != 'all' && !empty($categories) && $source == 'wp_posts') {
								$query .= '&category_name='.$categories.'';	
							}
								
							if($pagination == 'yes' || $pagination == 'load-more') {
								$query .= '&posts_per_page='.$num_posts_page.'';	
							} else {
								if($num_posts == '') { $num_posts = '-1'; }
								$query .= '&posts_per_page='.$num_posts.'';
							}
						
							// PAGINATION		
							if($pagination == 'yes' || $pagination == 'load-more') {
								if ( get_query_var('paged') ) {
									$paged = get_query_var('paged');
								
								} elseif ( get_query_var('page') ) {			
									$paged = get_query_var('page');			
								} else {			
									$paged = 1;			
								}			
								$query .= '&paged='.$paged.'';
							}
							// #PAGINATION	
						
						} else { // IF STICKY
							

							if($pagination == 'yes' || $pagination == 'load-more') {
								$num_posts = $num_posts_page;	
							} else {
								if($num_posts == '') { $num_posts = '-1'; }
								$num_posts = $num_posts;
							}

							// PAGINATION		
							
							if ( get_query_var('paged') ) {
								$paged = get_query_var('paged');							
							} elseif ( get_query_var('page') ) {			
								$paged = get_query_var('page');			
							} else {			
								$paged = 1;			
							}			
							
							// #PAGINATION	
												
							/* STICKY POST DA FARE ARRAY PER SCRITTURA IN ARRAY */
						
							$sticky = get_option( 'sticky_posts' );
							$sticky = array_slice( $sticky, 0, 5 );
							if($view_order == 'views') { 
								$query = array(
									'post_type' => 'post',
									'post_status' => 'publish',
									'orderby' 	=> $orderby,
									'order' => $order,
									'category_name' => $categories,
									'posts_per_page' => $num_posts,
									'meta_key' => 'wpb_post_views_count',
									'paged' => $paged, 
									'post__in'  => $sticky,
									'ignore_sticky_posts' => 1
								);
							} else {
								$query = array(
									'post_type' => 'post',
									'post_status' => 'publish',
									'orderby' 	=> $orderby,
									'order' => $order,
									'category_name' => $categories,
									'posts_per_page' => $num_posts,
									'paged' => $paged, 
									'post__in'  => $sticky,
									'ignore_sticky_posts' => 1
								);
							}						
							
						} // #all_posts
						
						return $query;	
	}
}