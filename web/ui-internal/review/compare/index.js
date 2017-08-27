"use strict"

$(document).ready(function () {
    $('[data-object=left]').elevateZoom({containLensZoom: true, zoomType: "lens", lensShape: "round", lensSize: 400, scrollZoom: true});
    $('[data-object=right]').elevateZoom({containLensZoom: true, zoomType: "lens", lensShape: "round", lensSize: 400, scrollZoom: true});
})