DiscourseEmbed = {
    discourseUrl: 'https://keskustelu.kokeilunpaikka.fi/',
    discourseEmbedUrl: $('#discourse-comments').attr('data-embed-url')
};

$(document).ready(function() {
    (function() {
        var d = document.createElement('script');
        d.type = 'text/javascript';
        d.async = true;
        d.src = DiscourseEmbed.discourseUrl + 'javascripts/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(d);
    })();

});
