<!-- пример на поиск товаров -->
<h2>Поиск товара</h2>
<div class="panel panel-default">
    <div class="panel-heading">Поиск товара</div>
    <div class="panel-body">
        <p>Пример поиска товаров, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/product/find</strong> с "<strong>query</strong>"
            содержащей поисковую фразу.</p>
        <p>Можно передать <strong>limit</strong>, для ограничения количества выдаваемых товаров. (50 максимум.)</p>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <input name="product-find" style="width: 400px;" type="text" placeholder="Поисковая фраза" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input name="product-find-submit" type="submit" value="Поиск" class="form-control">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-product-find"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=product-find-submit]').on('click', function(){
                    var query = $('input[name=product-find]').val();
                    $.ajax({url: '/api/product/find', type: 'POST', data: {
                        query: query,
                        limit: 3
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>
</div>

<!-- пример на получение категорий -->
<h2>Получение категорий</h2>
<div class="panel panel-default">
    <div class="panel-heading">Получение категорий</div>
    <div class="panel-body">
        <p>Пример получения категорий, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/activity/get-by-category</strong> с "<strong>query</strong>" содержащей ID сферы деятельности.</p>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <input name="category-find" style="width: 400px;" type="text" placeholder="Идентификатор категории / сферы деятельности" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input name="category-find-submit" type="submit" value="Поиск" class="form-control">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-category-find"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=category-find-submit]').on('click', function(){
                    var query = $('input[name=category-find]').val();
                    $.ajax({url: '/api/activity/get-by-category', type: 'POST', data: {
                        category_id: category_id
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>
</div>

<!-- пример на поиск характеристики -->
<h2>Поиск характеристик</h2>
<?
    \common\assets\SelectizeAsset::register($this);
?>
<style>
    .selectize-control {width: 500px};
</style>
<div class="panel panel-default">
    <div class="panel-heading">Поиск характеристик</div>
    <div class="panel-body">
        <p>Пример поиска характеристик, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/vocabulary/find</strong> с "<strong>query</strong>" содержащей название характеристики.</p>
        <blockquote>
            <p>Не много логики.</p>
        </blockquote>
        <p>
            При отправке запроса на поиск характеристики скрипт будет искать уже в существующих характеристиких.<br>
            Если такие найдутся то у них будет id больше 0 а так же присутствовать поле `correcting` равное 0.<br>
            Если характеристики не найдутся вообще а так же слово было введено с ошибкой, к примеру "ширАта" вместо "широта" <br>
            то сервер вернет исправленое слово опираясь на словари яндекса, ответ будет иметь поле `correcting` равное 1, а так же `id` = 0.
        </p>
        <ul>
            <li>`id` больше 0 - характеристика нашлась.</li>
            <li>`id` равное 0 - характеристика не нашлась.</li>
            <li>`correcting` равное 1 - характеристика возможно была введена не корректно и исправлена.</li>
        </ul>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <select name="vocabulary-find" placeholder="Введите искомое название характеристики" class="form-control"></select>
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-vocabulary-find"></code>
        </pre>
        <pre>
            Пример JS (не текущий, т.к. на приведенном примере плагин selectize.)
            <code data-language="javascript">
                $('input[name=vocabulary-find]').change(function(){
                    var query = $('input[name=vocabulary-find]').val();
                    $.ajax({url: '/api/vocabulary/find', type: 'POST', data: {
                        query: query
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>

    <div class="panel-heading">Получение возможных значений характеристики</div>
    <div class="panel-body">
        <p>При выборе найденной характеристики необходимо отображать её возможные значения. Для этого нужно отправить
            <strong>AJAX-POST</strong> запрос на URL <strong>/api/vocabulary/terms</strong> "<strong>vocabulary_id</strong>" содержащей id характеристики.</p>

        <div class="form-group">
            <label>
                <input style="width: 500px;" placeholder="ID характеристики" value="6" type="text" name="vocabulary_id" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="submit" name="get-terms" class="btn">
            </label>
        </div>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - Запрос прошел успешно.</li>
            <li>1 - Значений характеристики нет.</li>
            <li>2 - Не передан идентификатор характеристики.</li>
            <li>3 - Характеристика не найдена.</li>
        </ul>
        <pre>
            Пример ответа
            <code id="result-get-terms"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=get-terms]').click(function() {
                    var id = $('input[name=firm_id]').val();
                    var vocabulary_id = $('input[name=vocabulary_id]').val();
                    $.ajax({url: '/api/vocabulary/terms', type: 'POST', data: {
                        vocabulary_id: vocabulary_id
                    }})
                    .success(function(data){
                        $('code#result-get-terms').html(JSON.stringify(data));
                    });
                });
            </code>
        </pre>
    </div>

</div>


<!-- пример на поиск организации по названию либо бин -->
<h2>Поиск организации</h2>
<div class="panel panel-default">
    <div class="panel-heading">Поиск организации</div>
    <div class="panel-body">
        <p>Пример поиска организации, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/firm/find</strong> с "<strong>query</strong>" содержащей название либо БИН организации.</p>
        <p>В случае если <strong>query</strong> число из 12 цифр то поиск идет по БИН'у организации</p>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <input style="width: 500px;" placeholder="Название организации или БИН" type="text" name="firm" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="submit" name="search-firm" class="btn">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-firm"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=search-firm]').click(function(){
                    var query = $('input[name=firm]').val();
                    $.ajax({url: '/api/firm/find', type: 'POST', data: {
                        query: query
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>
</div>

<!-- пример на поиск профиля организации по названию либо бин -->
<h2>Поиск профиля организации</h2>
<div class="panel panel-default">
    <div class="panel-heading">Поиск профиля организации</div>
    <div class="panel-body">
        <p>Пример поиска профиля организации, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/profile/find</strong> с "<strong>query</strong>" содержащей название либо БИН организации.</p>
        <p>В случае если <strong>query</strong> число из 12 цифр то поиск идет по БИН'у организации</p>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <input style="width: 500px;" placeholder="Название организации или БИН" type="text" name="profile" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="submit" name="search-profile-firm" class="btn">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-profile"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=search-profile-firm]').click(function(){
                    var query = $('input[name=firm]').val();
                    $.ajax({url: '/api/profile/find', type: 'POST', data: {
                        query: query
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>
</div>

<!-- получение данных профиля организации по id. -->
<h2>Данные профиля организации</h2>
<div class="panel panel-default">
    <div class="panel-heading">Данные профиля организации</div>
    <div class="panel-body">
        <p>Пример получения данных профиля организации, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/profile/get</strong></p>
        <strong>Возможные параметры:</strong>
        <ul>
            <li>id - идентификатор организации (параметр обязательный)</li>
            <li>columns - необходимые поля (параметр обязательный),<br>
                возможные значения: (id, title, bin)<br>
                Можно перечислять через запятую, например: id,title,bin (без пробелов!!!)
                <span class="label label-success">При необходимости список пополню.</span>
            </li>
        </ul>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - Организация отсутствует.</li>
            <li>2 - Отсутствуют колонки.</li>
            <li>3 - Не корректный формат колонок.</li>
        </ul>
        <div class="form-group">
            <label>
                <input style="width: 500px;" placeholder="ID организации" value="1" type="text" name="firm_id" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input style="width: 500px;" value="bin,title" placeholder="Необходимые колонки" type="text" name="firm_columns" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="submit" name="get-firm" class="btn">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-get-firm"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=get-firm]').click(function() {
                    var id = $('input[name=firm_id]').val();
                    var firm_columns = $('input[name=firm_columns]').val();
                    $.ajax({url: '/api/profile/get', type: 'POST', data: {
                        id: id,
                        columns: firm_columns
                    }})
                    .success(function(data){
                        $('code#result-get-firm').html(JSON.stringify(data));
                    });
                });
            </code>
        </pre>
    </div>
</div>

<!-- пример на поиск города -->
<h2>Поиск города</h2>
<div class="panel panel-default">
    <div class="panel-heading">Поиск города</div>
    <div class="panel-body">
        <p>Пример поиска города, нужно отправить <strong>AJAX-POST</strong> запрос на URL <strong>/api/location/find</strong> с "<strong>query</strong>" равному искомому городу.</p>
        <strong>Возможные коды ошибок:</strong>
        <ul>
            <li>0 - запрос прошел успешно.</li>
            <li>1 - отправляемая поисковая фраза пуста.</li>
            <li>2 - системная ошибка, если возникло какое-либо исключение при выполнении.</li>
        </ul>
        <div class="form-group">
            <label>
                <input type="text" name="location" placeholder="Название города" class="form-control">
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="submit" name="search-location" class="btn">
            </label>
        </div>
        <pre>
            Пример ответа
            <code id="result-location"></code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('input[name=search-location]').click(function(){
                    var location = $('input[name=location]').val();
                    $.ajax({url: '/api/location/find', type: 'POST', data: {
                        query: location
                    }})
                    .success(function(data){
                        console.log(data);
                    });
                });
            </code>
        </pre>
    </div>
</div>


<!-- пример на загрузку фото -->
<h2>Загрузка фото</h2>
<div class="panel panel-default">
    <div class="panel-heading">Загрузка фото</div>
    <div class="panel-body">
        <p>
            Пример привязки плагина JQuery File Upload, нужно чтобы плагин слал запросы на URL /api/uploader/upload
            <span class="label label-success">Можно глянуть в консоль браузера после загрузки файла для просмотра результата.</span>
        </p>
        <pre>
            Пример HTML
            <code data-language="html">
                &lt;input id=&quot;file-upload&quot; type=&quot;file&quot; name=&quot;files[]&quot; data-url=&quot;/api/uploader/upload&quot; multiple&gt
            </code>
        </pre>
        <pre>
            Пример JS
            <code data-language="javascript">
                $('#file-upload').fileupload({
                    dataType: 'json',
                    // если не нужна миниатюра - убираем formData
                    formData: {
                        thumbnails: JSON.stringify({
                            width: 50, // обязательна для ресайза
                            height: 50,
                            type: 'CROP' // возможные типы (CROP, NONE, WIDTH, HEIGHT, AUTO)
                        })
                    },
                    done: function (e, data) {
                        console.log(data.result.files);
                    }
                });
            </code>
        </pre>
        <input id="file-upload" type="file" name="files[]" data-url="/api/uploader/upload" multiple>
    </div>
</div>