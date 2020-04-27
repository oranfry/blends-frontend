(function(){
    var $instanceform = $('#instanceform');
    var $contextform = $('#contextform');

    window.newid = function(){
        return Math.floor(Math.random() * 16777215).toString(16);
    };

    window.closeModals = function() {
        $('.modal--open, .inline-modal--open').removeClass('modal--open inline-modal--open');
        $('.modal-breakout').remove();
    };

    $('.adhoc-toggle').on('click', function(){
        var adhocvalue = prompt("New value");

        if (adhocvalue) {
            var $select = $(this).prev();
            var $option = $('<option>' + adhocvalue + '</option>');

            $option.insertAfter($select.children().first());
            $select.val(adhocvalue);
            $select.change();
        }
    });

    $('.fromtoday').on('click', function(e){
        e.preventDefault();
        var today = new Date();

        $(this).prevAll().each(function() {
            if ($(this).is('input')) {
                $(this).val(today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0'));
            }
        });
    });

    $('.instances-trigger').on('click', function(){
        $('.instances').toggleClass('open');
    });

    var manip = function(){
        var manips_string = $(this).data('manips');

        if (!manips_string) {
            return;
        }

        var manips = manips_string.split('&');

        for (var i = 0; i < manips.length; i++) {
            var cv_name = manips[i].split('=')[0];
            var cv_value = manips[i].split('=')[1];
            var matches = cv_value.match(/^base64:(.*)/);
            var value;

            if (matches !== null) {
                value = atob(matches[1]);
            } else {
                value = cv_value;
            }

            $('[name="' + cv_name + '"]').val(value);
        }
    }

    $('a.cv-manip').on('click', function(e) {
        e.preventDefault();
        manip.call(this);
        changeInstance();
    });

    $('input.cv-manip:not(.cv-surrogate), select.cv-manip:not(.cv-surrogate)').on('change', function(e) {
        manip.call(this);
        changeInstance();
    });

    $('.cv-surrogate').on('change', function(e){
        e.preventDefault();
        var for_cv = $(this).data('for');
        var value = $(this).is('[type="checkbox"]') && $(this).is(':checked') || $(this).val() || null;
        var $for = $instanceform.find('[name="' + for_cv + '"]');

        $for.val(value);

        if ($(this).is('.cv-manip')) {
            manip.call(this);
        }

        if (!$(this).is('.no-autosubmit')) {
            changeInstance();
        }
    });

    $('.cv').on('change', function(e){
        changeInstance();
    });

    $('#contextform [name="context"]').on('change', function(e){
        $contextform.submit();
    });

    $('.modal-trigger').on('click', function(e){
        e.preventDefault();

        var done = false;
        var $modal = $('#' + $(this).data('for'));

        $modal.addClass('modal--open');
        $('body').append($('<div class="modal-breakout">'));
    });

    $('body').on('click', '.modal-breakout', closeModals);
    $('.close-modal').on('click', closeModals);

    $('.inline-modal-trigger').on('click', function(e){
        e.preventDefault();

        var done = false;

        $(this).prevAll().each(function() {
            if (done) {
                return;
            }

            if ($(this).is('.inline-modal')) {
                $(this).addClass('inline-modal--open');
                $(this).css({width: '', left: '', right: ''});

                var that = this;

                var leftHidden = function () {
                    return $(that).offset().left < 15;
                };

                var rightHidden = function () {
                    return $(that).offset().left + $(that).width() > $(window).width() - 15;
                };

                if (leftHidden()) {
                    var right = 0;
                    for (var right = 0; right < 100 && leftHidden(); right++) {
                        $(this).css('right', -right + 'px');
                    }
                } else if (rightHidden()) {
                    var left = 0;
                    for (var left = 0; left < 1000 && rightHidden(); left++) {
                        $(this).css({width: $(this).width() + 'px', left: -left + 'px'});
                    }
                }

                $('<div class="modal-breakout" style="background-color: transparent">').insertAfter(this);
                done = true;
            }
        });
    });

    $('body').on('click', '.modal-breakout', closeModals);

    $('.open-custom-daterange:not(.current)').on('click', function(e){
        e.preventDefault();
        $('.custom-daterange, .standard-daterange').toggle();
    });

    $('.bulk-edit-form input[name="action"]').on('click', function(e){
        e.preventDefault();

        var $editForm = $(this).closest('form');
        var form = $editForm[0];
        var data = {};
        var blend = $editForm.data('blend');
        var $selected = $('tr[data-id].selected');
        var query;

        $("input[name^='apply_']:checked").each(function() {
            var rel_field = $(this).attr('name').replace(/^apply_/, '');
            var $rel_field = $editForm.find('[name="' + rel_field + '"]');
            data[rel_field] = $rel_field.val();
        });

        if (!Object.keys(data).length) {
            closeModals();
            return;
        }

        if ($selected.length) {
            query = getSelectionQuery($selected);
        } else {
            query = getFiltersQuery();
        }

        var handleSave = function() {
            $.ajax('/api/blend/' + BLEND_NAME + '/update?' + query, {
                method: 'post',
                contentType: false,
                processData: false,
                data: JSON.stringify(data),
                success: function(data) {
                    window.location.reload();
                },
                error: function(data){
                    alert(data.responseJSON && data.responseJSON.error || 'Unknown error');
                }
            });
        };

        var $fileInputs = $editForm.find('input[type="file"]');
        var numLoadedFiles = 0;

        if (!$fileInputs.length) {
            handleSave();
        }

        $fileInputs.each(function(){
            var $input = $(this);
            var file = $input[0].files[0];
            delete data[$input.attr('name')];

            if (!file) {
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }

                return;
            }

            var reader = new FileReader();

            reader.onload = function(event) {
                data[$input.attr('name')] = btoa(event.target.result);
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }
            };

            reader.readAsBinaryString(file);
        });
    });

    $('.bulk-add-form input[name="action"]').on('click', function(e){
        var $addForm = $(this).closest('form');
        e.preventDefault();

        var form = $addForm[0];
        var data = {};
        var linetype = $addForm.data('linetype');
        var blend = $addForm.data('blend');

        $addForm.find("[name]").each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        var handleSave = function() {
            $.ajax('/api/' + blend + '/' + linetype + '/add?' + getFiltersQuery(), {
                method: 'post',
                contentType: false,
                processData: false,
                data: JSON.stringify(data),
                success: function(data) {
                    window.location.reload();
                },
                error: function(data){
                    alert(data.responseJSON && data.responseJSON.error || 'Unknown error');
                }
            });
        };

        var $fileInputs = $addForm.find('input[type="file"]');
        var numLoadedFiles = 0;

        if (!$fileInputs.length) {
            handleSave();
        }

        $fileInputs.each(function(){
            var $input = $(this);
            var file = $input[0].files[0];
            delete data[$input.attr('name')];

            if (!file) {
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }

                return;
            }

            var reader = new FileReader();

            reader.onload = function(event) {
                data[$input.attr('name')] = btoa(event.target.result);
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }
            };

            reader.readAsBinaryString(file);
        });
    });

    $('.recordaction_delete, .recordaction_unlink').on('click', function(e){
        var $assoc = $(this).closest('.assoc');
        $assoc.find('input, select').not(this).val(null);
    });

    $('.trigger-edit-line').on('click', function(event){
        event.preventDefault();
        var $row = $(this).closest('tr');
        var id = $row.data('id');
        var type = $row.data('type');
        var back = window.location.pathname + window.location.search;

        window.location.href = '/' + type + '/' + id + '?back=' + btoa(back);
    });

    $('.trigger-bulk-delete-lines').on('click', function(event){
        event.preventDefault();

        if (!confirm('bulk delete?')) {
            return;
        }

        var $selected = $('tr[data-id].selected');
        var query;

        if ($selected.length) {
            query = getSelectionQuery($selected);
        } else {
            query = getFiltersQuery();
        }

        $.ajax('/api/blend/' + BLEND_NAME + '/delete?' + query, {
            method: 'post',
            data: {},
            success: function(data) {
                alert(data[2] + ' records and ' + data[3] + ' links deleted by ' + data[0] + ' queries in ' + data[1] + ' seconds');
                window.location.reload();
            },
            error: function(data){
                alert(data.responseJSON.error);
            }
        });
    });

    $('.trigger-bulk-print-lines').on('click', function(event)
    {
        event.preventDefault();

        var $selected = $('tr[data-id].selected');
        var query;

        if (!confirm('bulk print?')) {
            return;
        }

        if ($selected.length) {
            query = getSelectionQuery($selected);
        } else {
            query = getFiltersQuery();
        }

        $.ajax('/api/blend/' + BLEND_NAME + '/print?' + query, {
            method: 'post',
            data: {},
            error: function(data){
                alert(data.responseJSON.error);
            }
        });
    });

    $(document).on('scroll', function(){
        $('body').toggleClass('hasscroll', document.documentElement.scrollTop > 0);
    });

    var onResize = function() {
        if ($('.calendar-month').length && $('body').height() < $(window).height()) {
            var avail = $(window).height() - $('.daterow').first().offset().top - ($('body').width() >= 800 && 10 || 0);
            var each = Math.min($(window).height() / 5, (Math.floor(avail / $('.eventrow').length) - $('.daterow').first().height())) + 'px';
            $('.eventcell').css('height', each);
        } else {
            $('.eventcell').css('height', '');
        }

        $('.cvdump-standin').css('height', $('.cvdump').height() + 'px');

        $('.samewidth').each(function(){
            var $children = $(this).find('> *');
            var max = 0;

            $children.css({width: '', display: 'inline-block'});

            $children.each(function(){
                max = Math.max(max, $(this).outerWidth());
            });

            $children.css({width: Math.ceil(max) + 'px', display: '', margin: '0 auto 1em auto'});
        });


        $('br + .navset').prev().remove();

        var prevNavsetTop = null;

        $('.navset').each(function(){
            var navsetTop = $(this).offset().top;
            var nobar = (navsetTop != prevNavsetTop);

            $(this).toggleClass('navset--nobar', nobar);

            if (prevNavsetTop !== null && navsetTop != prevNavsetTop) {
                $('<br>').insertBefore($(this));
            }

            prevNavsetTop = navsetTop;
        });

        $('.navbar-placeholder').height($('.navbar').outerHeight() + 'px');
    };

    $(window).on('resize', onResize);

    onResize();

    $('#filter-form select').on('change', function(){
        $('#filter-form input[type="text"]').focus();
    });

    $('#filter-form button').on('click', function(e){
        var $form = $(this).closest('#filter-form');

        var $select = $form.find('select');
        var field = $select.val();

        if (!field) {
            return;
        }

        var $input = $form.find('input[type="text"]');
        var val = $input.val();

        var filterid = newid();
        var $filters = $('[name="' + BLEND_NAME + '_filters__value"]');
        var filters = $filters.val() + ($filters.val() && ',' || '') + filterid;

        var $field = $('<input name="' + filterid + '__field">');
        var $cmp = $('<input name="' + filterid + '__cmp">');
        var $value = $('<input name="' + filterid + '__value">');

        $field.val(field);
        $cmp.val('=');
        $value.val(val);

        $('#new-vars-here')
            .first()
            .append($field)
            .append($cmp)
            .append($value);

        $filters.val(filters);
        $filters.change();
        closeModals();
    });

    $('#change-blend').on('change', function(){
        window.location.href = '/blend/' + $(this).val();
    });

    $('.print-line').on('click', function(){
        $.ajax('/' + LINETYPE_NAME + '/' + LINE_ID + '/print', {
            method: 'post',
            success: function(data) { $('#output').html(data.messages.join(', ')); }
        });
    });

    $('.trigger-delete-line, .trigger-unlink-line').on('click', function(){
        var action = $(this).hasClass('trigger-delete-line') && 'delete' || 'unlink';
        var $row = $(this).closest('tr');
        var id = $row.data('id');
        var type = $row.data('type');
        var parent = $row.data('parent');

        if (!confirm(action + ' ' + type + ' ' + id + '?')) {
            return;
        }

        var query = '';

        if (parent) {
            query = '?&parent=' + parent;
        }

        $.ajax('/api/' + type + '/' + id + '/' + action + query, {
            method: 'post',
            data: {},
            success: function() {
                window.location.reload();
            },
            error: function(data){
                alert(data.responseJSON.error);
            }
        });
    });

    $('.edit-form input[name="action"]').on('click', function(e){
        e.preventDefault();

        var $form = $(this).closest('form');
        var formData = new FormData($form[0]);
        var buttonClicked = $(this).val();

        var url = '/' + LINETYPE_NAME;

        if (LINE_ID) {
            url += '/' + LINE_ID;
        }

        url += '/save';

        var data = Object.fromEntries(formData);

        var handleSave = function() {
            $.ajax(url, {
                method: 'post',
                contentType: false,
                processData: false,
                data: JSON.stringify(data),
                success: function(data) {
                    window.location.href = back;
                },
                error: function(data){
                    alert(data.responseJSON.error);
                }
            });
        };

        var $fileInputs = $form.find('input[type="file"]');
        var numLoadedFiles = 0;

        if (!$fileInputs.length) {
            handleSave();
        }

        $fileInputs.each(function(){
            var $input = $(this);
            var file = $input[0].files[0];
            delete data[$input.attr('name')];

            if (!file) {
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }

                return;
            }

            var reader = new FileReader();

            reader.onload = function(event) {
                data[$input.attr('name')] = btoa(event.target.result);
                numLoadedFiles++;

                if (numLoadedFiles == $fileInputs.length) {
                    handleSave();
                }
            };

            reader.readAsBinaryString(file);
        });
    });

    var repeaterChanged = function(){
        if ($('.repeater-select').val()) {
            var r = new RegExp($('.repeater-select').val());

            $('.repeater-modal [data-repeaters]').each(function(){
                $(this).toggle($(this).data('repeaters').match(r) !== null);
            });
        } else {
            $('.repeater-modal [data-repeaters]').hide();
        }
    };

    $('.repeater-select').on('change', repeaterChanged);
    repeaterChanged();

    $('.easy-table tr').on('click', function(e){
        var $table = $(this).closest('table');
        var $tbody = $(this).closest('tbody');
        var $block;

        if ($(this).data('id')) {
            $(this).toggleClass('selected');
            return;
        }

        if ($tbody.length) {
            $block = $tbody;
        } else {
            $block = $table;
        }

        var $trs = $block.find('tr[data-id]');
        var selected = $trs.filter('.selected').length > 0;
        $trs.toggleClass('selected', !selected);
    });

    function getJsonFromUrl(url)
    {
        var question = url.indexOf("?");
        var hash = url.indexOf("#");

        if (hash==-1 && question==-1) {
            return {};
        }

        if (hash==-1) {
            hash = url.length;
        }

        var query = question == -1 || hash == question + 1 ? url.substring(hash) : url.substring(question + 1, hash);
        var result = {};

        query.split("&").forEach(function(part) {
            if (!part) {
                return;
            }

            part = part.split("+").join(" "); // replace every + with space, regexp-free version

            var eq = part.indexOf("=");
            var key = eq > -1 ? part.substr(0, eq) : part;
            var val = eq > -1 ? decodeURIComponent(part.substr(eq + 1)) : "";
            var from = key.indexOf("[");

            if (from==-1) {
                result[decodeURIComponent(key)] = val;
            } else {
                var to = key.indexOf("]", from);
                var index = decodeURIComponent(key.substring(from + 1,to));
                key = decodeURIComponent(key.substring(0, from));

                if (!result[key]) {
                    result[key] = [];
                }

                if (!index) {
                    result[key].push(val);
                } else {
                    result[key][index] = val;
                }
            }
        });

        return result;
    }

    function getQueryParams()
    {
        var existingData = getJsonFromUrl(location.href);
        var instanceData = Object.fromEntries(new FormData($instanceform[0]));
        var data = $.extend(existingData, instanceData);

        // remove nullish
        for (var prop in data) {
            if (Object.prototype.hasOwnProperty.call(data, prop)) {
                if (!data[prop]) {
                    delete data[prop];
                }
            }
        }

        return data;
    }

    function getFiltersQuery()
    {
        var queryParams = getQueryParams();

        delete queryParams._returnurl;
        delete queryParams.back;

        return $.param(queryParams);
    }

    function getSelectionQuery($selected)
    {
        var deepids = $selected.map(function(){
            return $(this).data('type') + ':' + $(this).data('id');
        }).get();

        return 'selection=' + deepids.join(',');
    }

    function changeInstance()
    {
        var base = location.href.split('?')[0];
        var data = getQueryParams();

        delete data._returnurl;

        var query = $.param(data);
        var link = base + (query && '?' || '') + query;

        window.location.href = link;
    }
})();
