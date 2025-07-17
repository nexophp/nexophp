function ajax(url, data, call) {
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        data: data,
        success: function (res) {
            call(res);
        },
        error: function (xhr, textStatus, errorThrown) {
        },
    });
}

Vue.prototype.$ELEMENT = { size: 'medium' };
Vue.mixin({
    methods: {

    }
});

$(document).ready(function () { }).keydown(
    function (e) {
        if (e.which === 27) {
            layer.closeAll();
        }
    });

/**
 *  下载文件 
 */
function down_file(url, fileName) {
    const x = new XMLHttpRequest()
    x.open('GET', url, true)
    x.responseType = 'blob'
    x.onload = function () {
        const url = window.URL.createObjectURL(x.response)
        const a = document.createElement('a')
        a.href = url
        a.download = fileName
        a.click()
    }
    x.send()
}