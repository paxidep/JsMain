webpackJsonp([5],{"./src/common/constants/apiServerConstants.js":function(e,n){e.exports={API_SERVER:"",STATIC_SERVER:""}},"./src/login/containers/LogoutPage.js":function(e,n,t){"use strict";function o(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}function r(e,n){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!n||"object"!=typeof n&&"function"!=typeof n?e:n}function c(e,n){if("function"!=typeof n&&null!==n)throw new TypeError("Super expression must either be null or a function, not "+typeof n);e.prototype=Object.create(n&&n.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),n&&(Object.setPrototypeOf?Object.setPrototypeOf(e,n):e.__proto__=n)}Object.defineProperty(n,"__esModule",{value:!0});var i=t("./node_modules/react/react.js"),u=t.n(i),a=t("./src/common/components/CookieHelper.js"),s=t("./node_modules/axios/index.js"),f=t.n(s),l=function(){function e(e,n){for(var t=0;t<n.length;t++){var o=n[t];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(n,t,o){return t&&e(n.prototype,t),o&&e(n,o),n}}(),p=t("./src/common/constants/apiServerConstants.js"),b=function(e){function n(e){return o(this,n),r(this,(n.__proto__||Object.getPrototypeOf(n)).call(this,e))}return c(n,e),l(n,[{key:"componentDidMount",value:function(){f.a.get(p.API_SERVER+"/static/logoutPage").then(function(e){t.i(a.c)("AUTHCHECKSUM"),window.location.href="/login"})}},{key:"render",value:function(){return null}}]),n}(u.a.Component);n.default=b}});