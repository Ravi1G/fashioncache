<?php
/**
 * The template for displaying Category pages
 *
 * Used to display archive-type pages for posts in a category.
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
	           	<div class="categoryHeadingWithMargins"><h1><?php printf( __( 'Category : %s', 'twentytwelve' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1></div>
	            <div class="blogSection"> 
	           		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				

			<?php if ( category_description() ) : // Show an optional category description ?>
				<div class="archive-meta"><?php echo category_description(); ?></div>
			<?php endif; ?>
			</header><!-- .archive-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
				get_template_part( 'content', get_post_format() );
			endwhile;

			//twentytwelve_content_nav( 'nav-below' );
			?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
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
	
	

	<section id="primary" class="site-content">
		<div id="content" role="main">

		

		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>