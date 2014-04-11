<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header(); ?>
	<div class="container content blog standardContainer">
	    <div class="SiteContentSection">
	        <div class="SiteContentLeft">            
	           	<div class="categoryHeadingWithMargins"><h1><?php the_title();?></h1></div>
	            <div class="blogSection"> 
	           	<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
					
				<?php endwhile; // end of the loop. ?>
	            </div>  
	            <div class="cb"></div>   
		   </div>
	        <?php get_sidebar();?>
			<?php //@todo put fashion experts here ?>
	    </div>   
	</div>

	

<?php get_sidebar(); ?>
<?php get_footer(); ?>