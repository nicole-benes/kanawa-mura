// * Bootstrap libraries
import "./_bootstrap";


(function($) {
     $( '.navbar-toggler' ).click( function() {
          if( $( this ).hasClass( 'collapsed' ) ) {
               $( '.navbar.fixed-top' ).removeClass( 'menu-expanded' );
          } else {
               $( '.navbar.fixed-top' ).addClass( 'menu-expanded' );               
          }
     });
})(jQuery);
