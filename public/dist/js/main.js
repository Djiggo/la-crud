$(function () {
    $("ul.sortable, table.sortable tbody").sortable({
        placeholder: "ui-state-highlight"
    });
    $("ul.sortable, table.sortable tbody").disableSelection();

//    $('.dropdown').hover(function() {
//        $(this).toggleClass('open');
//    });


    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: '&#x3c;Пред',
        nextText: 'След&#x3e;',
        currentText: 'Сегодня',
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
            'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);

    $('.timepicker').timepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd HH:mm:00",
        showHour: true,
        showMinute: true
    });

    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd 00:00:00"
    });

    // http://www.tinymce.com/wiki.php/Configuration
    tinymce.init({
        selector: "textarea.html_editor",
        width: "auto",
        language: "ru",
        height: 300,
        force_p_newlines: false,
        invalid_styles: {
            "*": 'font-size font font-style background float font-family letter-spacing line-height margin margin-left margin-right margin-top margin-bottom '
        },
        plugins: [
            "responsivefilemanager advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor"
        ],
        toolbar1: "undo redo | styleselect   | \n\
                  bullist numlist outdent indent | link image | media fullpage\n\
                    bold italic underline strikethrough superscript subscript | \n\
                 alignleft aligncenter alignright alignjustify  | code",
        resize: "both",
        external_filemanager_path: "/assets/admin/plugins/filemanager/",
        filemanager_title: "Файловый менеджер",
        external_plugins: {"filemanager": "../filemanager/plugin.min.js"},
        menubar: false,
        relative_urls: false,
        browser_spellcheck: true
    });

});

var auto = {
    helpers: {
        guid: function () {
            function s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            }

            return function () {
                return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                    s4() + '-' + s4() + s4() + s4();
            };
        }
    },
    utils: {
        // Encodes an ISO-8859-1 string to UTF-8
        utf8_encode: function (str_data) {

            str_data = str_data.replace(/\r\n/g, "\n");
            var utftext = "";

            for (var n = 0; n < str_data.length; n++) {
                var c = str_data.charCodeAt(n);
                if (c < 128) {
                    utftext += String.fromCharCode(c);
                } else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                } else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
            }

            return utftext;
        },

        md5: function (str) {


            var RotateLeft = function (lValue, iShiftBits) {
                return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
            };

            var AddUnsigned = function (lX, lY) {
                var lX4, lY4, lX8, lY8, lResult;
                lX8 = (lX & 0x80000000);
                lY8 = (lY & 0x80000000);
                lX4 = (lX & 0x40000000);
                lY4 = (lY & 0x40000000);
                lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
                if (lX4 & lY4) {
                    return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
                }
                if (lX4 | lY4) {
                    if (lResult & 0x40000000) {
                        return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                    } else {
                        return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                    }
                } else {
                    return (lResult ^ lX8 ^ lY8);
                }
            };

            var F = function (x, y, z) {
                return (x & y) | ((~x) & z);
            };
            var G = function (x, y, z) {
                return (x & z) | (y & (~z));
            };
            var H = function (x, y, z) {
                return (x ^ y ^ z);
            };
            var I = function (x, y, z) {
                return (y ^ (x | (~z)));
            };

            var FF = function (a, b, c, d, x, s, ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            var GG = function (a, b, c, d, x, s, ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            var HH = function (a, b, c, d, x, s, ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            var II = function (a, b, c, d, x, s, ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            var ConvertToWordArray = function (str) {
                var lWordCount;
                var lMessageLength = str.length;
                var lNumberOfWords_temp1 = lMessageLength + 8;
                var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
                var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
                var lWordArray = Array(lNumberOfWords - 1);
                var lBytePosition = 0;
                var lByteCount = 0;
                while (lByteCount < lMessageLength) {
                    lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                    lBytePosition = (lByteCount % 4) * 8;
                    lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount) << lBytePosition));
                    lByteCount++;
                }
                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                lBytePosition = (lByteCount % 4) * 8;
                lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
                lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
                lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
                return lWordArray;
            };

            var WordToHex = function (lValue) {
                var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
                for (lCount = 0; lCount <= 3; lCount++) {
                    lByte = (lValue >>> (lCount * 8)) & 255;
                    WordToHexValue_temp = "0" + lByte.toString(16);
                    WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
                }
                return WordToHexValue;
            };

            var x = Array();
            var k, AA, BB, CC, DD, a, b, c, d;
            var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
            var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
            var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
            var S41 = 6, S42 = 10, S43 = 15, S44 = 21;

            str = this.utf8_encode(str);
            x = ConvertToWordArray(str);
            a = 0x67452301;
            b = 0xEFCDAB89;
            c = 0x98BADCFE;
            d = 0x10325476;

            for (k = 0; k < x.length; k += 16) {
                AA = a;
                BB = b;
                CC = c;
                DD = d;
                a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
                d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
                c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
                b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
                a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
                d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
                c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
                b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
                a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
                d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
                c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
                b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
                a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
                d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
                c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
                b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
                a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
                d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
                c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
                b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
                a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
                d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
                c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
                b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
                a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
                d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
                c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
                b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
                a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
                d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
                c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
                b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
                a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
                d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
                c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
                b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
                a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
                d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
                c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
                b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
                a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
                d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
                c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
                b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
                a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
                d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
                c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
                b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
                a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
                d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
                c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
                b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
                a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
                d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
                c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
                b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
                a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
                d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
                c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
                b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
                a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
                d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
                c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
                b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
                a = AddUnsigned(a, AA);
                b = AddUnsigned(b, BB);
                c = AddUnsigned(c, CC);
                d = AddUnsigned(d, DD);
            }

            var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

            return temp.toLowerCase();
        }
    }

};

jQuery(function ($) { //CRUD widgets

    $(".crud_delete_link").click(function (e) {
        if (!confirm("Точно удалить?")) {
            e.preventDefault();
        }
    });


    $(".crud_car_images_widget .new_images_uploader").change(function () {

        var form_data = new FormData();

        form_data.append('mod_id', $(this).data('mod_id'));

        $.each($(this).prop('files'), function (key, value) {
            form_data.append(key, value);
        });

        $.ajax({
            url: '/admin/crud_car_images_widget/upload_images',
            type: 'POST',
            data: form_data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function (data) {
                if (data.success) {
                    $.each(data.files, function (key, value) {

                        $(".crud_car_images_widget ul").append(
                            '<li class="ui-sortable-handle">' +
                            '   <input type="hidden" name="images[]" value="' + value.filename + '">' +
                            '   <img src="/' + value.full_filename + '" height="150">' +
                            '</li>'
                        );
                    });
                }
            }
        });


    });

    $(".crud_car_images_widget .delete_car_image span").click(function () {
        $(this).closest("li").remove();
    });

    $(".crud_md5_widget").change(function () {
        var name = $(this).data('name');
        var md5_input = $("[name=" + name + "]");

        var md5 = md5_input.data('default');

        if ($(this).val().length > 0) {
            md5 = auto.utils.md5($(this).val());
        }

        md5_input.val(md5);

    });

    $('.crud_timestamp_widget > span').click(function () {
        $(this).siblings("input").datetimepicker("show")
    });

    $('.crud_timestamp_widget > input').datetimepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        controlType: 'select',
        oneLine: true,
        onSelect: function (ev, a) {
            var date = new Date(ev.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));

            var input_name = $(this).data('name');
            var timestamp = (date.getTime() / 1000 | 0).toFixed();
            $(this).closest(".form-group ").find("[name=" + input_name + "]").val(timestamp);

        }
    });


    $('.crud_date_widget > span').click(function () {
        $(this).siblings("input").datetimepicker("show")
    });

    $('.crud_date_widget > input').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        controlType: 'select'
    });

    $(".crud_image_widget input").change(function (e) {
        $(this).siblings('img').attr("src", $(this).data('full_path'));
    });

    $(".crud_image_widget a").click(function (e) {
        e.preventDefault();
        $(this).siblings('input').click()
    });

    $(".crud_container_model_widget").pqSelect({
        bootstrap: {on: true},
        width: "100%",
        singlePlaceholder: ""
    });

    $(".crud-widget select").pqSelect({
        bootstrap: {on: true},
        width: "100%",
        singlePlaceholder: ""
    });


    (function () {

        $(".crud_news_tags_widget textarea")
            .autocomplete({
                source: function (request, response) {
                    $.getJSON("/admin/news_tags_widget/search_tags", {
                        term: request.term.split(/,\s*/).pop()
                    }, response);
                },
                search: function () {
                    // Минимальная ширина
                    var term = this.value.split(/,\s*/).pop();
                    if (term.length < 1) {
                        return false;
                    }
                },
                focus: function () {
                    // prevent value inserted on focus
                    return false;
                },
                select: function (event, ui) {
                    var terms = this.value.split(/,\s*/);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }

            });
    })();

});

jQuery(function ($) {

    $('.filemanager').each(function () {
        if (!$(this).attr("id")) {
            var field_id = auto.helpers.guid();
            $(this).attr("id", field_id);
        }
    });

    $('.filemanager').attr('readonly', 'readonly');
    $('.filemanager').attr('placeholder', 'Кликни, чтобы изменить');

    $('.filemanager').change(function () {

        $(this).data('full_path', $(this).val());

        if ($(this).data('only_filename')) {
            var old_val = $(this).val();
            var sections = old_val.split('/');
            $(this).val(sections[sections.length - 1]);
        }
    });

    $('.filemanager').click(function () {
        var field_id = $(this).attr("id");

        var url_args = '';

        if ($(this).data('uploaddir')) {
            url_args += "&uploaddir=" + $(this).data('uploaddir');
        }

        $.colorbox({
            iframe: true,
            ajax: true,
            href: '/assets/admin/plugins/filemanager/dialog.php?type=2&relative_url=0&field_id=' + field_id + url_args,
            'width': 900,
            'height': 600
        });
    });

    function metaDescChange() {
        var meta_left_el = $(this).siblings("small").find(".meta_left");
        var meta_limit = parseInt($(this).siblings("small").find(".meta_limit").html());
        var char_count = $(this).val().length;
        meta_left_el.html(meta_limit - char_count);
    }

    $("textarea[name=meta_desc]").keyup(metaDescChange);
    $("textarea[name=meta_desc]").change(metaDescChange);

});


jQuery(document).ready(function () {
    jQuery('#save').click(function () {
        document.save.submit();
    });
    jQuery("#select_all").click(function (data) {
        if (data.target.checked === true) {
            jQuery(".delete  input:checkbox:enabled").prop('checked', true);
        } else {
            jQuery(".delete  input:checkbox:enabled").prop('checked', false);
        }
    });
    jQuery('#delete').click(function () {
        if (confirm("Вы подтверждаете удаление?")) {
            document.delete.submit();
            return true;
        } else {
            return false;
        }
    });
});