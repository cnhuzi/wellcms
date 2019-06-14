/*
* 对图片进行缩略，裁剪，base64 存入 form 隐藏表单
* name 与 file 控件相同
* 上传过程中
* 禁止 button，对图片可以缩略
* */
$.fn.base64_encode_file = function (width, height, action) {
    var action = action || 'thumb';
    var jform = $(this);
    var jsubmit = jform.find('input[type="submit"]');
    jform.on('change', 'input[type="file"]', function (e) {

        var jfile = $(this);
        var jassoc = jfile.data('assoc') ? $('#' + jfile.data('assoc')) : null;
        var obj = e.target;
        jsubmit.button('disabled');
        var file = obj.files[0];

        /*燃烧的冰20181111 删除同一个域*/
        $('.input_' + jassoc.attr('id')).removeDeep();
        /*燃烧的冰20181111 创建一个隐藏域，用来保存 base64 数据*/
        var jhidden = $('<input class="input_' + jassoc.attr('id') + '" type="hidden" name="' + obj.name + '" />').appendTo(jform);
        //obj.name = '';

        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (e) {
            /*如果是图片，并且设置了，宽高，和剪切模式*/
            if (width && height && xn.substr(this.result, 0, 10) == 'data:image') {
                xn.image_resize(this.result, function (code, message) {
                    if (code == 0) {
                        if (jassoc) jassoc.attr('src', message.data);
                        jhidden.val(message.data); // base64
                    } else {
                        alert(message);
                    }
                    jsubmit.button('reset');
                }, {width: width, height: height, action: action});
            } else {
                if (jassoc) jassoc.attr('src', this.result);
                jhidden.val(this.result);
                jsubmit.button('reset');
            }
        }
    });
};