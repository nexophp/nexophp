const E = window.wangEditor || {};



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

$(function () {
    $('.select2').select2();
});
/**
 * 是否是图片
 * @param {string} url 
 */
function isImageFile(url) {
    const ext = get_ext(url);
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    return imageExtensions.includes(ext);
}
/**
 * formatFileSize
 * @param {number} size - 文件大小（字节）
 * @returns {string} 格式化后的文件大小
 */
function formatFileSize(size) {
    if (size === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(size) / Math.log(k));
    return parseFloat((size / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
