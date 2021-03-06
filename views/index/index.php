<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var $model /c006/crud/model/Crud */

$this->title = 'CRUD';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .title {
        display    : block;
        font-size  : 2em;
        margin-top : 20px;
        color      : #e7ad24;
    }

    label[for=crud-databasetables] {
        display : block;
    }

    .inline-elements {
        display : table;
        width   : 100%;
    }

    .inline-elements > div {
        display        : table-cell;
        vertical-align : top;
    }

    .inline-elements > div:first-of-type {
        width : 50%;
    }

    .inline-elements > div:last-of-type {
        width : 50%;
    }

    .inline-elements select {
        min-width : 100%;
    }

    .inline-elements input {
        min-width : 100%;
    }

    .namespace-add {
        cursor : pointer;
    }

    .namespace-add:hover {
        color : #ff6a00;
    }
</style>

<div id="content">

    <div class="form">
        <?php $form = ActiveForm::begin([
            'id'     => 'form-crud',
            'action' => (Yii::$app->urlManager->enablePrettyUrl) ? '/crud/default/process' : 'index.php?r=crud/index/process',
        ]);
        ?>
        <div class="title-large">Auto CRUD</div>

        <div class="inline-elements">
            <div>
                <?= $form->field($model, 'namespace')->hint('<span class="namespace-add">frontend</span> | <span class="namespace-add">backend</span> | <span class="namespace-add">common</span> | <span class="namespace-add">vendor</span>') ?>
            </div>
            <div>
                <?= $form->field($model, 'db_connection')->hint('Default is "db". This refers to "Yii::$app->db"') ?>
            </div>
        </div>

        <div class="inline-elements">
            <div>
                <?= $form->field($model, 'models_path')->hint('Namespace path to the models directory') ?>
            </div>
            <div>
                <?= $form->field($model, 'models_search_path')->hint('Namespace path to the model search directory') ?>
            </div>
        </div>


        <?= $form->field($model, 'controllers_path')->hint('Views will be added relative to the controller path') ?>


        <div class="inline-elements">
            <div style="width: 80%;">
                <?= $form->field($model, 'database_tables')->dropDownList(['00' => ' '] + $tables)->hint('Select tables') ?>
            </div>
            <div style="width: 20%; vertical-align: middle; text-align: right">
                <button id="button-add-all" class="btn btn-primary" type="button">Add all tables</button>
            </div>
        </div>


        <div class="inline-elements">
            <div style="width: 80%">
                <?= $form->field($model, 'tables')
                    ->label('Tables to Process')
                    ->hint('Change to textarea and back to easily view tables') ?>
            </div>
            <div style="width: 20%; vertical-align: middle; text-align: right">
                <?= Html::button('Change View', ['class' => 'btn btn-success', 'id' => 'button-tables-convert']) ?>
            </div>
        </div>


        <div class="inline-elements">
            <div>
                <?= $form->field($model, 'process_models')->dropDownList(['No', 'Yes'])->hint('Skip or run processing models') ?>
            </div>
            <div>
                <?= $form->field($model, 'exclude_models')->hint('Do Not add .php') ?>
            </div>
        </div>


        <div class="inline-elements">
            <div>
                <?= $form->field($model, 'process_controllers')->dropDownList(['No', 'Yes'])->hint('Skip or run processing controllers') ?>
            </div>
            <div>
                <?= $form->field($model, 'exclude_controllers')->hint('Do Not add .php') ?>
            </div>
        </div>

        <div class="inline-elements">
            <div>
                <?= $form->field($model, 'crud_template')->hint('') ?>
            </div>
            <div>
                <?= $form->field($model, 'crud_template_path')->hint('Leave blank for default. Example: @c006/crud/templates/custom') ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Run', ['class' => 'btn btn-primary', 'name' => 'button-submit']) ?>
        </div>

        <?php ActiveForm::end() ?>

        <?php /* This is optional if SubmitSpinner is installed */ ?>
        <?php if (class_exists('c006\\spinner\\SubmitSpinner')) : ?>
            <?= c006\spinner\SubmitSpinner::widget(['form_id' => $form->id]); ?>
        <?php endif ?>

    </div>
</div>

<script type="text/javascript">
    jQuery(function () {
        jQuery('#button-add-all')
            .click(function () {
                var $tables = jQuery('#crud-tables');
                $tables.val("");
                jQuery("#crud-database_tables > option")
                    .each(function () {
                        if (this.text != 'migration' && this.text != 'user') {
                            $tables.val($tables.val() + ',' + this.text);
                        }
                    });
                $tables.val($tables.val().replace(/^,+/, ''));
            });

        jQuery('#crud-namespace')
            .bind('blur', function () {
                var $this = jQuery(this);
                var _namespace = $this.val();
                _namespace = _namespace.replace(/^\//, '');
                _namespace = _namespace.replace(/$\//, '');
                jQuery('#crud-models_path').val(_namespace + '/models');
                jQuery('#crud-models_search_path').val(_namespace + '/models/search');
                jQuery('#crud-controllers_path').val(_namespace + '/controllers');
            });

        jQuery('#crud-database_tables')
            .bind('change',
            function () {
                var val = jQuery(this).find('option:selected').text();
                if (val) {
                    var $elm = jQuery('#crud-tables');
                    var val2 = $elm.val().replace(val + ',', '');
                    val = val2 + ',' + val;
                    val = val.replace(/\s+/gi, '').replace(/,+/gi, ',').replace(/^,/, '');
                    $elm.val(val);
                }
            });

        jQuery('.namespace-add')
            .click(function () {
                var $this = jQuery(this);
                jQuery('#crud-namespace').val($this.html()).focus().blur();
            });

        jQuery('#button-tables-convert')
            .click(function () {
                var $this = jQuery('#crud-tables');
                var $parent = $this.parent();
                if ($this.attr('type') == "text") {
                    var $textarea = jQuery(document.createElement('textarea'));
                    $textarea.attr('id', $this.attr('id'));
                    $textarea.attr('type', 'textarea');
                    $textarea.attr('class', $this.attr('class'));
                    $textarea.attr('name', $this.attr('name'));
                    $textarea.html($this.val().replace(/\s+/g, '').replace(/,/g, "\n"));
                    $this.remove();
                    jQuery($textarea).insertAfter($parent.find('> label'));
                } else {
                    var $input = jQuery(document.createElement('input'));
                    $input.attr('id', $this.attr('id'));
                    $input.attr('type', 'text');
                    $input.attr('class', $this.attr('class'));
                    $input.attr('name', $this.attr('name'));
                    $input.val($this.html().replace(/[\r\n]/g, ", "));
                    $this.remove();
                    jQuery($input).insertAfter($parent.find('> label'));
                }
                jQuery('#crud-tables').blur();
            });
    });
</script>

