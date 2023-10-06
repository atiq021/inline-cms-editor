@if(!isset($editable) || !$editable)
    @php
        $editable=false;
    @endphp
@endif

<div class="loading-overlay">
    <span class="fas fa-spinner fa-3x fa-spin"></span>
</div>

<div class="modal fade" id="editorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit Text</h5>
          <button type="button" class="close sbxeditor-btn-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editable-xpath" />
            <input type="hidden" id="edit_type" />

            <div class="form-group">
                <label for="editable-img" class="col-form-label">Image:</label>
                <input type="file" class="form-control" id="editable-img" />
            </div>
            <div class="form-group">
              <label for="editable-text" class="col-form-label">Text:</label>
              <textarea class="form-control" name="editable-text" id="editable-text"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary sbxeditor-btn-close">Close</button>
          <button type="button" class="sbxeditor-btn-save btn btn-primary">Save</button>
        </div>
      </div>
    </div>
</div>

@if($editable == true)
    <style>

        img.sbx-inline-img-editor {
            border: 3px dotted black;
            cursor: pointer;
        }

        .sbx-inline-editor{
            position: relative;
        }

        .sbx-inline-editor i {
            content: "\f303";
            position: absolute;
            z-index: 999;
            background: red;
            width: 20px;
            height: 20px;
            border-radius: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'FontAwesome' !important;
            font-size: 10px;
            font-weight: 400;
            right: -15px;
            top: -25%;
            color: white;
            cursor: pointer;
        }

        a.sbx-img-edit {
            position: absolute;
            left: 227px;
            background: red;
            padding: 10px;
            border: none;
            border-radius: 35px;
            height: 35px;
            width: 35px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            z-index: 99999;
        }

        .loading-overlay {
            display: none;
            background: rgb(237 237 237);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.is-active {
            display: flex;
        }
    </style>
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.21.0/ckeditor.js" integrity="sha512-ff67djVavIxfsnP13CZtuHqf7VyX62ZAObYle+JlObWZvS4/VQkNVaFBOO6eyx2cum8WtiZ0pqyxLCQKC7bjcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    
    var editor = CKEDITOR.replace('editable-text', {
        allowedContent: true,
    });

    $(document).on('click', '.sbxeditor-btn-close', function () {
        $('#editorModal').modal('hide');
    })

    var numberOfSections=0;
    var lastChangedElement;
    var keyPressed=0;
    $('.loading-overlay').addClass('is-active');
    $(document).ready(function () {
        $.ajax({
            type: "GET",
            url: "{{route('sbx.getSetting')}}",
            data: {
                slug: 'landing2',
            },
            success: function(data){
                console.log(data);
                for (let i = 0; i < data.length; i++) {
                    if(data[i].value == null || data[i].value == ''){
                        continue;
                    }

                    var xpath = data[i].key;
                    var element = getElementByXpath(xpath);
                    if(element == null || element == ''){
                        continue;
                    }

                    if(data[i].is_image == 1){
                        element.setAttribute('src', data[i].value);
                    }else{
                        element.innerText = data[i].value;
                    }
                }

                if("{{$editable}}" == true){
                    $('.sbx-inline-editor').append('<i class="fa fa-pen"></i>');
                }
                $('.loading-overlay').removeClass('is-active');
            }
        });
        
    });

    if("{{$editable}}" == true){
        $('.sbxeditor-btn-save').on('click', function () {
            var xpath = $('#editorModal #editable-xpath').val();
            var element = getElementByXpath(xpath);
            if($('#editorModal #edit_type').val() == 'img'){
                var value = $('#editorModal #editable-img')[0];
                var img = window.URL.createObjectURL(value.files[0]);
                element.setAttribute('src', img);
                saveFile(element, value.files[0]);
            }else{
                element.innerHTML = editor.getData();
                $(element).append('<i class="fa fa-pen"></i>');
                saveSetting(element);
            }
            $('#editorModal').modal('hide');
        });

        function saveSetting(element) {
            var key = getPathTo(element), value=element.innerText;
            $.ajax({
                type: "POST",
                url: "{{route('sbx.setSetting')}}",
                data: {
                    _token: "{{@csrf_token()}}",
                    slug: 'landing2',
                    key: key,
                    value: value
                },
                success: function(data){
                    alert('Updated Successfully');
                }
            });
        }

        function saveFile(element, file) {
            var key = getPathTo(element);
            var formData = new FormData();
            formData.append('_token', "{{@csrf_token()}}");
            formData.append('key', key);
            formData.append('value', file);
            formData.append('slug', 'landing2');

            $.ajax({
                type: "POST",
                url: "{{route('sbx.setSetting')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    alert('Updated Successfully');
                }
            });
        }

        $(document).on('click', '.sbx-inline-editor i', function(e){
            $('#editorModal #edit_type').val('text');
            $('#editorModal #editable-xpath').val(getPathTo(e.target.parentNode));
            $('#editorModal #editable-img').closest('.form-group').hide();
            editor.setData(e.target.parentNode.innerHTML)
            $('#editorModal #editable-text').closest('.form-group').show();
            $('#editorModal').modal('show');
        });

        $(document).on('click', '.sbx-inline-img-editor', function(e){
            $('#editorModal #edit_type').val('img');
            $('#editorModal #editable-xpath').val(getPathTo(e.target));
            $('#editorModal #editable-text').val('');
            $('#editorModal #editable-text').closest('.form-group').hide();
            $('#editorModal #editable-img').closest('.form-group').show();
            $('#editorModal').modal('show');
        });
    }


    function getElementByXpath(path) {
        return document.evaluate(path, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
    }

    $(document).on('click', '.sbx-img-edit', function (e) {
        $('#sbx-file').trigger('click');
        $(this).addClass('sbx-set-img');
        console.log('image clicked');
    });

    $('#sbx-file').on('change', function (e) {
        console.log(e.target.files);
        var file = e.target.files[0];
        var img = window.URL.createObjectURL(file);
        $('img.sbx-set-img').attr('src', img);
        $('img').removeClass('sbx-set-img');
        $('#sbx-file').val('');
    });

    function getPathTo(element) {
        if (element.tagName == 'HTML')
            return '/HTML[1]';
        if (element===document.body)
            return '/HTML[1]/BODY[1]';

        var ix= 0;
        var siblings= element.parentNode.childNodes;
        for (var i= 0; i<siblings.length; i++) {
            var sibling= siblings[i];
            if (sibling===element)
                return getPathTo(element.parentNode)+'/'+element.tagName+'['+(ix+1)+']';
            if (sibling.nodeType===1 && sibling.tagName===element.tagName)
                ix++;
        }
    }
</script>