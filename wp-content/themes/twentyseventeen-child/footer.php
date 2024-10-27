</div><!-- #content -->

<footer id="colophon" class="site-footer">
	<div class="wrap">
		<div>
			<?php
			$fields = get_option('theme_options_manager_fields', []);

			if (!empty($fields)) {
				echo '<div class="custom-fields">';
				foreach ($fields as $field) {
					echo '<div class="field">';
					echo '<strong>' . esc_html($field['name']) . ($field['name'] ? ':' : '') . '</strong> ';
					echo esc_html($field['value']);
					echo '</div>';
				}
				echo '</div>';
			}
			?>
		</div>
		<?php
		get_template_part( 'template-parts/footer/footer', 'widgets' );

		if ( has_nav_menu( 'social' ) ) :
			?>
			<nav class="social-navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentyseventeen' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'social',
						'menu_class'     => 'social-links-menu',
						'depth'          => 1,
						'link_before'    => '<span class="screen-reader-text">',
						'link_after'     => '</span>' . twentyseventeen_get_svg( array( 'icon' => 'chain' ) ),
					)
				);
				?>
			</nav><!-- .social-navigation -->
		<?php
		endif;

		get_template_part( 'template-parts/footer/site', 'info' );
		?>
	</div><!-- .wrap -->
</footer><!-- #colophon -->
</div><!-- .site-content-contain -->
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>