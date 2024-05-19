// * Bootstrap libraries
import ScrollSpy from "bootstrap/js/dist/scrollspy";
import "./_bootstrap";


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
/*
     $( '.dropdown-toggle' ).click( function() {
          if( $( '.navbar-collapse' ).hasClass( 'show' ) ) {
               if( $( this ).hasClass( 'show' ) ) {
                    $( '.navbar-collapse' ).addClass( 'expanded' );
               } else {
                    $( '.navbar-collapse' ).removeClass( 'expanded' );
               }
               console.log( 'dsffdfsd' );
          }
     });
*/     

     const scrollSpyWrapper  = document.getElementById( 'scrollspy-wrapper' );

     const spy = new ScrollSpy( scrollSpyWrapper, {
          target: '#null',
          threshold: [ 0, 0, 0 ],
          smoothScroll: true
     });
     
     let oldRing = '';

     scrollSpyWrapper.addEventListener( 'activate.bs.scrollspy', function( event ) {
          if( event.relatedTarget.hash.includes( 'mastery' ) ) {
               let hash = event.relatedTarget.hash;

               $( '.side-pane-mastery-link' ).removeClass( 'active' );
               $( '.side-pane-mastery-link[href="' + hash + '"]' ).addClass( 'active' );

               const ring = hash.substring( hash.indexOf( '#' ) + 1, hash.indexOf( '-' ) );

               if( oldRing != ring ) {
                    $( '.side-pane-navigation-ring-wrapper' ).removeClass( 'active' );
                    $( '#side-pane-navigation-' + ring ).addClass( 'active' );
               }
          }
     });
})(jQuery);
