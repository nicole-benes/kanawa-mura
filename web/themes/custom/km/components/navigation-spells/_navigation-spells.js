// * Bootstrap libraries
import ScrollSpy from "bootstrap/js/dist/scrollspy";
import "../../src/js/_bootstrap";


(function($) {
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
                    $( '.side-ring-wrapper' ).removeClass( 'active' );
                    $( '#side-pane-navigation-' + ring ).addClass( 'active' );
               }
          }
     });
})(jQuery);
