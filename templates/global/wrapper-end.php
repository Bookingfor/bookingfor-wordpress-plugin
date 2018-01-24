<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/bookingfor/global/wrapper-end.php.
 *
 * HOWEVER, on occasion BookingFor will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		BookingFor
 * @package 	BookingFor/Templates
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch( $template ) {
	case 'BookYourTravel':
	global $post, $bookyourtravel_theme_globals;

	$page_id = $post->ID;
	$page_custom_fields = get_post_custom( $page_id);
	$page_sidebar_positioning = null;
	if (isset($page_custom_fields['page_sidebar_positioning'])) {
		$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
		$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
	}
    ?>
    	</section>
	<!--//full-width content--> 
	<?php 
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
	?>
</div>

    <?php 
    
    break;
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar( 'searchavailability' );
		echo '</div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	case 'twentyseventeen' :
	?>
		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->
<?php
		break;
	case 'royal' :
?>
				</div>

				<?php get_sidebar(); ?>

			</div><!-- end row-fluid -->

		</div>
	</div><!-- end container -->
<?php 
		break;
	case 'saladmag' :
	?>
<div class="brack_space"></div>
        </div>

  </div>
  <!-- End content -->
   
    <!-- Start sidebar -->
	<div class="four columns" id="sidebar"> 

                <?php
				
				if(isset($GLOBALS['sbg_sidebar'][0])){
					$custom_sidebar = $GLOBALS['sbg_sidebar'][0];
					
					$page_sidebar = of_get_option('page_sidebar','');	
					if(!empty($page_sidebar)) {
						$custom_sidebar = $page_sidebar;
					}
				
					foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
					if($sidebar['name'] == $custom_sidebar)
			  			{
							 $dyn_side = $sidebar['id'];
						}
					} 
				}			

				if(isset($dyn_side)) {
					
					if (is_active_sidebar($dyn_side)) { dynamic_sidebar($dyn_side);}
	
				} else{
					if (is_active_sidebar('general-sidebar')) { dynamic_sidebar('general-sidebar'); }
				}					
				
				
?>
  </div>
  <!-- End sidebar -->

          

</div>
 </section>
<!-- end content --> 
<?php
		break;
	case 'realhomes' :
?>
                    </div>

                </div><!-- End Main Content -->

            </div> <!-- End span9 -->

            <?php get_sidebar('pages'); ?>

        </div><!-- End contents row -->

    </div><!-- End Content -->
<?php 
		break;
	case 'x' :
	case 'x-child' :
?>
    </div>

    <?php get_sidebar(); ?>

  </div>
<?php 
		break;
	case 'genesis' :
		do_action( 'genesis_after_loop' );
		genesis_markup( array(
			'html5' => '</main>', //* end .content
			'xhtml' => '</div>', //* end #content
		) );
		do_action( 'genesis_after_content' );
		
		echo '</div>'; //* end .content-sidebar-wrap or #content-sidebar-wrap
		do_action( 'genesis_after_content_sidebar_wrap' );
	
	break;

	default :
		echo '</div></div>';
		break;
}
