<?php

if ( is_user_logged_in() ) {
	greenpath_membership_template_part( 'widgets/login-opener', 'templates/logged-in-content' );
} else {
	greenpath_membership_template_part( 'widgets/login-opener', 'templates/logged-out-content' );
}
