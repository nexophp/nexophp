if (typeof window.wangEditor !== 'undefined') {
    const E = window.wangEditor;
}


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
 * 获取文件扩展名
 * @param {string} filename - 文件名或路径
 * @param {boolean} [toLowerCase=true] - 是否转为小写
 * @returns {string} 文件扩展名（不带点）
 */
function get_ext(filename, toLowerCase = true) {
    if (!filename || typeof filename !== 'string') return '';

    // 处理URL中的查询参数和哈希
    const cleanName = filename.split(/[?#]/)[0];

    // 获取最后一个点后的内容
    const ext = cleanName.slice(
        Math.max(
            cleanName.lastIndexOf('.'),
            cleanName.lastIndexOf('/') + 1
        ) + 1
    );

    return toLowerCase ? ext.toLowerCase() : ext;
}

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

$(function(){
    $('.select2').select2();
});