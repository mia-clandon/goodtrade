<?php

namespace backend\components\form\controls;

use common\libs\form\Form;

/**
 * Class SearchFirm
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchFirm extends SearchSelect {

    protected $url              = '/api/firm/find';
    protected $value_field      = 'id';
    protected $label_field      = 'title';
    protected $search_field     = 'title';
    protected $request_method   = Form::METHOD_POST;
    protected $query_field      = 'query';

    protected $query_data       = [
        'format' => 'bin_with_title',
    ];

    protected $template_name = 'select';

}