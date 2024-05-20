import "../../src/js/_bootstrap";

(function($) {
     $( '.navbar-toggler' ).click( function() {
          if( $( this ).hasClass( 'collapsed' ) ) {
               $( '.navbar.fixed-top' ).removeClass( 'menu-expanded' );
          } else {
               $( '.navbar.fixed-top' ).addClass( 'menu-expanded' );               
          }
     });

     $( '.navbar-nav li.dropdown a' ).click( function() {
          if( !$( '.navbar-collapse' ).hasClass( 'show' ) ) {
               window.location.href = $( this ).attr( 'href' );
          } 
     });
})(jQuery);
