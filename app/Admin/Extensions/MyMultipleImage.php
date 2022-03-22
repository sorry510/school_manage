<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\MultipleImage;

class MyMultipleImage extends MultipleImage
{
    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.multiplefile';

    /**
     * @param string $options
     */
    protected function setupScripts($options)
    {
        $this->script = <<<EOT
$("input{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        if ($this->fileActionSettings['showRemove']) {
            $text = [
                'title' => trans('admin.delete_confirm'),
                'confirm' => trans('admin.confirm'),
                'cancel' => trans('admin.cancel'),
            ];

            $this->script .= <<<EOT
$("input{$this->getElementClassSelector()}").on('filebeforedelete', function() {

    return new Promise(function(resolve, reject) {

        var remove = resolve;

        swal({
            title: "{$text['title']}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "{$text['confirm']}",
            showLoaderOnConfirm: true,
            cancelButtonText: "{$text['cancel']}",
            preConfirm: function() {
                return new Promise(function(resolve) {
                    resolve(remove());
                });
            }
        });
    });
});
EOT;
        }

        if ($this->fileActionSettings['showDrag']) {
            $this->addVariables([
                'sortable' => true,
                'sort_flag' => static::FILE_SORT_FLAG,
            ]);

            $this->script .= <<<EOT
$("input{$this->getElementClassSelector()}").on('filesorted', function(event, params) {

    var order = [];

    params.stack.forEach(function (item) {
        order.push(item.key);
    });

    $("input{$this->getElementClassSelector()}_sort").val(order);
});
EOT;
        }

        $this->script .= <<<AUTO_UPLOAD_SCRIPT
$("input{$this->getElementClassSelector()}").on('filebatchselected', function (event, data, id, index) {

    $(this).fileinput("upload"); // 自动上传
});
AUTO_UPLOAD_SCRIPT;

        $this->script .= <<<HIDE_RIGHT_DELETE
$("input{$this->getElementClassSelector()}").parent().parent().parent().parent().find(".fileinput-remove").css({"display": "none"});
HIDE_RIGHT_DELETE;
    }
}
