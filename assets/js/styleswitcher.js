if($.cookie("color")) {
    $("link[href|='css/color']").attr("href","css/" + $.cookie("color"));
}

if($.cookie("width")) {
    $("link[href|='css/width']").attr("href","css/" + $.cookie("width"));
}

$(document).ready(function() { 
    $("#color-switcher-content .color").click(function() { 
        $("link[href|='css/color']").attr("href", "css/" + $(this).attr('rel'));
        $.cookie("color",$(this).attr('rel'), {expires: 7, path: '/'});
        return false;
    });

    $("#color-switcher-content .option").click(function() { 
        $("link[href|='css/width']").attr("href", "css/" + $(this).attr('rel'));
        console.log('test');
        $.cookie("width",$(this).attr('rel'), {expires: 7, path: '/'});
        return false;
    });
});