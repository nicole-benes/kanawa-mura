/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/main.script.js":
/*!*******************************!*\
  !*** ./src/js/main.script.js ***!
  \*******************************/
/***/ (function() {

(function ($) {
  $('.navbar-toggler').click(function () {
    if ($(this).hasClass('collapsed')) {
      $('.navbar.fixed-top').removeClass('menu-expanded');
    } else {
      $('.navbar.fixed-top').addClass('menu-expanded');
    }
  });
  $('.navbar-nav li.dropdown a').click(function () {
    if (!$('.navbar-collapse').hasClass('show')) {
      window.location.href = $(this).attr('href');
    }
  });
})(jQuery);

/***/ }),

/***/ "./components/page-navigation/page-navigation.scss":
/*!*********************************************************!*\
  !*** ./components/page-navigation/page-navigation.scss ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./components/page/page.scss":
/*!***********************************!*\
  !*** ./components/page/page.scss ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/scss/main.style.scss":
/*!**********************************!*\
  !*** ./src/scss/main.style.scss ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./components/navbar/navbar.scss":
/*!***************************************!*\
  !*** ./components/navbar/navbar.scss ***!
  \***************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./components/navigation-spells/navigation-spells.scss":
/*!*************************************************************!*\
  !*** ./components/navigation-spells/navigation-spells.scss ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./components/offcanvas/_offcanvas.scss":
/*!**********************************************!*\
  !*** ./components/offcanvas/_offcanvas.scss ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./components/page-footer/page-footer.scss":
/*!*************************************************!*\
  !*** ./components/page-footer/page-footer.scss ***!
  \*************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/build/js/main.script": 0,
/******/ 			"build/css/main.style": 0,
/******/ 			"components/page-footer/page-footer": 0,
/******/ 			"components/offcanvas/_offcanvas": 0,
/******/ 			"components/navigation-spells/navigation-spells": 0,
/******/ 			"components/navbar/navbar": 0,
/******/ 			"components/page/page": 0,
/******/ 			"components/page-navigation/page-navigation": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkkm"] = self["webpackChunkkm"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./src/js/main.script.js"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./src/scss/main.style.scss"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/navbar/navbar.scss"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/navigation-spells/navigation-spells.scss"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/offcanvas/_offcanvas.scss"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/page-footer/page-footer.scss"); })
/******/ 	__webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/page-navigation/page-navigation.scss"); })
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["build/css/main.style","components/page-footer/page-footer","components/offcanvas/_offcanvas","components/navigation-spells/navigation-spells","components/navbar/navbar","components/page/page","components/page-navigation/page-navigation"], function() { return __webpack_require__("./components/page/page.scss"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=main.script.js.map