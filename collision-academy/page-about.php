<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 *
 * Collision Academy — About Page Template
 *
 * WordPress automatically uses this template for a page with the slug "about".
 * The "Template Name" comment above also makes it selectable from the
 * Page Attributes panel in the editor (Appearance > Page Attributes > Template).
 *
 * Sections:
 * - Mission statement hero
 * - About the publication / author
 * - Credentials and background
 * - Future training platform teaser
 * - CTA to newsletter or contact
 *
 * @package CollisionAcademy
 */

get_header();
?>

<!-- About Hero -->
<div class="ca-page-hero ca-page-hero--about">
	<div class="ca-container">
		<span class="ca-eyebrow"><?php esc_html_e( 'About', 'collision-academy' ); ?></span>
		<h1 class="ca-page-hero__title"><?php esc_html_e( 'Advancing forensic collision knowledge.', 'collision-academy' ); ?></h1>
		<p class="ca-page-hero__sub">
			<?php esc_html_e( 'An independent publication dedicated to rigorous, evidence-based analysis of collision investigation, vehicle dynamics, and expert witness practice.', 'collision-academy' ); ?>
		</p>
	</div>
</div>

<div class="ca-container ca-page-about">

	<?php while ( have_posts() ) : the_post(); ?>

	<?php
	// If the about page has custom content in the editor, display it first.
	$page_content = get_the_content();
	if ( $page_content ) :
		?>
		<div class="ca-about-custom-content entry-content">
			<?php the_content(); ?>
		</div>
	<?php else : ?>

	<!-- Mission Section -->
	<section class="ca-about-section" aria-label="<?php esc_attr_e( 'Our mission', 'collision-academy' ); ?>">
		<div class="ca-about-section__inner ca-about-section--wide">
			<span class="ca-eyebrow"><?php esc_html_e( 'Mission', 'collision-academy' ); ?></span>
			<h2 class="ca-about-section__title"><?php esc_html_e( 'Why we exist', 'collision-academy' ); ?></h2>
			<div class="ca-about-section__body">
				<p>
					<?php esc_html_e( 'Forensic collision investigation is a discipline that sits at the intersection of engineering, physics, law, and human factors. It demands precision, intellectual rigour, and a commitment to evidence over assumption.', 'collision-academy' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Collision Academy was founded to serve the practitioners who work in this field — investigators, engineers, forensic scientists, barristers, solicitors, and expert witnesses — with analysis and commentary that matches their professional standards.', 'collision-academy' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Every article published here is evidence-based, technically grounded, and written by or for subject matter experts. We do not sensationalise. We do not speculate without foundation. We write as professionals, for professionals.', 'collision-academy' ); ?>
				</p>
			</div>
		</div>
	</section>

	<!-- About the Author / Organisation -->
	<section class="ca-about-section ca-about-section--alt" aria-label="<?php esc_attr_e( 'About the author', 'collision-academy' ); ?>">
		<div class="ca-about-section__inner">
			<span class="ca-eyebrow"><?php esc_html_e( 'The team', 'collision-academy' ); ?></span>
			<h2 class="ca-about-section__title"><?php esc_html_e( 'Who we are', 'collision-academy' ); ?></h2>
			<div class="ca-about-section__body">
				<?php
				// Show the site admin's bio here.
				$admin = get_userdata( 1 );
				if ( $admin && $admin->description ) :
					echo '<p>' . esc_html( $admin->description ) . '</p>';
				else :
					?>
					<p>
						<?php esc_html_e( 'Collision Academy is written and edited by an experienced forensic collision investigator and expert witness with extensive experience in the analysis of road traffic collisions for both prosecution and defence.', 'collision-academy' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Our contributors include practising investigators, academics, and legal professionals who bring specialist knowledge to each subject area.', 'collision-academy' ); ?>
					</p>
					<?php
				endif;
				?>
			</div>
		</div>
	</section>

	<!-- Credentials -->
	<section class="ca-about-section" aria-label="<?php esc_attr_e( 'Credentials and background', 'collision-academy' ); ?>">
		<div class="ca-about-section__inner">
			<span class="ca-eyebrow"><?php esc_html_e( 'Credentials', 'collision-academy' ); ?></span>
			<h2 class="ca-about-section__title"><?php esc_html_e( 'Our background', 'collision-academy' ); ?></h2>
			<div class="ca-about-section__body">
				<p>
					<?php esc_html_e( 'Content published on Collision Academy reflects real-world investigative experience, academic grounding, and ongoing engagement with case law, technical standards, and industry developments.', 'collision-academy' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'All technical content is reviewed for accuracy before publication. Where positions are taken on contested matters, they are presented with explicit reasoning and reference to evidence. Readers are encouraged to engage critically and to submit corrections or alternative perspectives for consideration.', 'collision-academy' ); ?>
				</p>
			</div>
		</div>
	</section>

	<!-- Training Platform Teaser -->
	<section class="ca-about-section ca-about-section--cta" aria-label="<?php esc_attr_e( 'Future training', 'collision-academy' ); ?>">
		<div class="ca-about-section__inner">
			<span class="ca-eyebrow"><?php esc_html_e( 'Coming soon', 'collision-academy' ); ?></span>
			<h2 class="ca-about-section__title"><?php esc_html_e( 'Training & CPD', 'collision-academy' ); ?></h2>
			<div class="ca-about-section__body">
				<p>
					<?php esc_html_e( 'We are developing a professional training platform to complement the publication — offering structured courses, CPD resources, and reference materials for collision investigators and associated professionals. Details will be announced in due course.', 'collision-academy' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Subscribe to the newsletter to be informed when training resources become available.', 'collision-academy' ); ?>
				</p>
			</div>
		</div>
	</section>

	<?php endif; // End else (no custom content) ?>

	<?php endwhile; ?>

	<!-- CTA Block -->
	<div class="ca-about-cta">
		<h2 class="ca-about-cta__title"><?php esc_html_e( 'Stay connected', 'collision-academy' ); ?></h2>
		<p class="ca-about-cta__text"><?php esc_html_e( 'Subscribe for expert analysis delivered to your inbox, or get in touch with a question or contribution.', 'collision-academy' ); ?></p>
		<div class="ca-about-cta__buttons">
			<a href="#newsletter" class="ca-btn ca-btn--primary"><?php esc_html_e( 'Subscribe', 'collision-academy' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ca-btn ca-btn--secondary"><?php esc_html_e( 'Contact us', 'collision-academy' ); ?></a>
		</div>
	</div>

</div><!-- .ca-container -->

<!-- Newsletter Signup -->
<section class="ca-section ca-section--newsletter" id="newsletter">
	<div class="ca-container ca-nl-section">
		<div class="ca-nl-section__text">
			<h2 class="ca-nl-section__title"><?php esc_html_e( 'Forensic intelligence, delivered.', 'collision-academy' ); ?></h2>
			<p class="ca-nl-section__sub"><?php esc_html_e( 'No spam. Unsubscribe at any time.', 'collision-academy' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/newsletter-form' ); ?>
	</div>
</section>

<?php get_footer(); ?>
