<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header(); ?>
	<div class="container content blog standardContainer">
	    <div class="SiteContentSection">
	        <div class="SiteContentLeft">            
	           	<div class="categoryHeadingWithMargins responsiveBlogHeading">
					<h1>BLOG</h1>
				</div>
	            <div class="blogSection"> 
	           		<?php if ( have_posts() ) : ?>
		           		<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>
					<?php else : ?>
						<div class="noPostMessage">No posts to show.</div>
	           		<?php endif;?>
	           		<div class="olderPost"><?php twentytwelve_content_nav( 'nav-below' ); ?></div>
	                <!-- <div class="olderPost"><a href="#">Older Posts&#x003E;</a></div> -->
	                <?php //posts_nav_link(); ?></p>
	            </div>  
	            <div class="cb"></div>   
		   </div>
	        <?php get_sidebar();?>
			<?php //@todo put fashion experts here ?>
	    </div>   
	</div>


	<?php /* ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php twentytwelve_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">

			<?php if ( current_user_can( 'edit_posts' ) ) :
				// Show a different message to a logged-in user who can add posts.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'No posts to display', 'twentytwelve' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'twentytwelve' ), admin_url( 'post-new.php' ) ); ?></p>
				</div><!-- .entry-content -->

			<?php else :
				// Show the default message to everyone else.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentytwelve' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'twentytwelve' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			<?php endif; // end current_user_can() check ?>

			</article><!-- #post-0 -->

		<?php endif; // end have_posts() check ?>

		</div><!-- #content -->
	</div><!-- #primary -->
	<?php */ ?>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>