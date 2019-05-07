/**
 *
 * Requerir Lodash
 */
window._ = require('lodash');


/**
 * Requerir jQuery y Bootstrap
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * Requerir Axios
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';