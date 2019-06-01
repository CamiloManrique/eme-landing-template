(function(){
    var mapLoader = function(url, attributes) {
        return function (element) {
            var iframe = document.createElement('iframe')
            iframe.setAttribute('src', url)
            $.each(attributes, (key, value) => {
                iframe.setAttribute(key, value)
            })
            element.html(iframe);
        }
    }

    $.fn.lazyLoadGoogleMap = function (url, attributes) {
        let $this = $(this)
        let callbackLoader = mapLoader(url, attributes)
        $this.attr('data-loader', "mapLoader")
        $this.lazy({
            mapLoader: callbackLoader
        })
    }
})()