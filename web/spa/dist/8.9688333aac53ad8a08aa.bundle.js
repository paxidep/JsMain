webpackJsonp([8],{

/***/ "./src/login/containers/LogoutPage.js":
/* exports provided: default */
/* all exports used */
/*!********************************************!*\
  !*** ./src/login/containers/LogoutPage.js ***!
  \********************************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("Object.defineProperty(__webpack_exports__, \"__esModule\", { value: true });\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(/*! react */ \"./node_modules/react/react.js\");\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__common_components_CookieHelper__ = __webpack_require__(/*! ../../common/components/CookieHelper */ \"./src/common/components/CookieHelper.js\");\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(/*! axios */ \"./node_modules/axios/index.js\");\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);\nvar _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();\n\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError(\"this hasn't been initialised - super() hasn't been called\"); } return call && (typeof call === \"object\" || typeof call === \"function\") ? call : self; }\n\nfunction _inherits(subClass, superClass) { if (typeof superClass !== \"function\" && superClass !== null) { throw new TypeError(\"Super expression must either be null or a function, not \" + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }\n\n\n\n\nvar API_SERVER_CONSTANTS = __webpack_require__(/*! ../../common/constants/apiServerConstants */ \"./src/common/constants/apiServerConstants.js\");\n\nvar LogoutPage = function (_React$Component) {\n\t_inherits(LogoutPage, _React$Component);\n\n\tfunction LogoutPage(props) {\n\t\t_classCallCheck(this, LogoutPage);\n\n\t\treturn _possibleConstructorReturn(this, (LogoutPage.__proto__ || Object.getPrototypeOf(LogoutPage)).call(this, props));\n\t}\n\n\t_createClass(LogoutPage, [{\n\t\tkey: \"componentDidMount\",\n\t\tvalue: function componentDidMount() {\n\t\t\t__WEBPACK_IMPORTED_MODULE_2_axios___default.a.get(API_SERVER_CONSTANTS.API_SERVER + \"/static/logoutPage\").then(function (response) {\n\t\t\t\t__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_1__common_components_CookieHelper__[\"b\" /* removeCookie */])(\"AUTHCHECKSUM\");\n\t\t\t\t// localStorage.clear();\n\t\t\t\twindow.location.href = \"/login\";\n\t\t\t});\n\t\t}\n\t}, {\n\t\tkey: \"render\",\n\t\tvalue: function render() {\n\t\t\treturn null;\n\t\t}\n\t}]);\n\n\treturn LogoutPage;\n}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (LogoutPage);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvbG9naW4vY29udGFpbmVycy9Mb2dvdXRQYWdlLmpzLmpzIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vL3NyYy9sb2dpbi9jb250YWluZXJzL0xvZ291dFBhZ2UuanM/ZTI2MyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgUmVhY3QgZnJvbSBcInJlYWN0XCI7XG5pbXBvcnQgeyByZW1vdmVDb29raWUgfSBmcm9tICcuLi8uLi9jb21tb24vY29tcG9uZW50cy9Db29raWVIZWxwZXInO1xuaW1wb3J0IGF4aW9zIGZyb20gXCJheGlvc1wiO1xubGV0IEFQSV9TRVJWRVJfQ09OU1RBTlRTID0gcmVxdWlyZSAoJy4uLy4uL2NvbW1vbi9jb25zdGFudHMvYXBpU2VydmVyQ29uc3RhbnRzJyk7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIExvZ291dFBhZ2UgZXh0ZW5kcyBSZWFjdC5Db21wb25lbnR7XG5cblx0Y29uc3RydWN0b3IocHJvcHMpe1xuXHRcdHN1cGVyKHByb3BzKTtcblx0fVxuXG5cdGNvbXBvbmVudERpZE1vdW50KCl7XG4gICAgICAgIGF4aW9zLmdldChBUElfU0VSVkVSX0NPTlNUQU5UUy5BUElfU0VSVkVSK1wiL3N0YXRpYy9sb2dvdXRQYWdlXCIpXG4gICAgICAgIC50aGVuKGZ1bmN0aW9uKHJlc3BvbnNlKXtcbiAgICAgICAgICAgIHJlbW92ZUNvb2tpZShcIkFVVEhDSEVDS1NVTVwiKTtcbiAgICAgICAgICAgIC8vIGxvY2FsU3RvcmFnZS5jbGVhcigpO1xuICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWY9XCIvbG9naW5cIjtcbiAgICAgICAgfSlcblx0fVxuXG5cdHJlbmRlcigpe1xuXHRcdHJldHVybiBudWxsO1xuXHR9XG59XG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHNyYy9sb2dpbi9jb250YWluZXJzL0xvZ291dFBhZ2UuanMiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUFFQTtBQUFBO0FBQ0E7QUFEQTtBQUVBO0FBQ0E7OztBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUFFQTtBQUNBO0FBQ0E7Ozs7QUFqQkE7QUFDQTtBQURBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/login/containers/LogoutPage.js\n");

/***/ })

});