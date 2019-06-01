window._ = require('lodash');


/**
 * Requerir jQuery y Bootstrap
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('jquery-lazy')
    require('lazy-load-youtube-videos')
    require('./lazy-load-google-map')
    require('bootstrap');
} catch (e) {}

/**
 * Requerir Axios
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let mapUrl = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6687.776858147367!2d-74.06200929942548!3d4.669439552246412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9af4f60a450f%3A0x9fb15e76de783733!2sCentro+M%C3%A9dico+Almirante+Col%C3%B3n!5e0!3m2!1ses!2sco!4v1552321299018"


$("#map").lazyLoadGoogleMap(mapUrl, {
    width: "100%",
    height: "450",
    frameborder: "0",
    style: "border:0",
    allowfullscreen: "1"
})