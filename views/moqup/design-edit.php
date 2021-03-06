<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('menu', ($moqup->isNewRecord) ? 'Add design' : 'Edit design');
$this->beginBlock('content-header-data');
$this->endBlock();
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header d-flex p-0">
        <h3 class="card-title p-3">
            <?= ($moqup->isNewRecord) ? Yii::t('moqup', 'Add Moqup') : Yii::t('moqup', 'Edit Moqup') ?>
        </h3>
    </div>
    <div class="card-body">
        
        <div class="alert alert-info">
            <h5><i class="icon fa fa-info"></i> Important!</h5>
            Use UI elements from <?= Html::a('AdminLTE 3', 'https://adminlte.io/themes/dev/AdminLTE/index3.html') ?>, <?= Html::a('Bootstrap 4', 'https://getbootstrap.com/docs/4.1/getting-started/introduction/') ?> and <?= Html::a('Font Awesome 5', 'https://fontawesome.com/icons') ?> examples.
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($moqup, 'title')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::ul([
                    Html::a('HTML', '#html', ['class' => 'nav-link active', 'data-toggle' => 'tab']),
                    Html::a('CSS (optional)', '#css', ['class' => 'nav-link', 'data-toggle' => 'tab']),
                    Html::a('Preview', '#preview', ['class' => 'nav-link', 'data-toggle' => 'tab', 'id' => 'toggle-prev']),
                ], [
                    'class' => 'nav nav-pills ml-auto p-2',
                    'encode' => false,
                    'itemOptions' => ['class' => 'nav-item']
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="html">
                        <?= $form->field($moqup, 'html')->textArea([
                            'rows' => 10,
                            'max-length' => 100000,
                        ])->label(false) ?>
                    </div>
                    <div class="tab-pane" id="css">
                        <?= $form->field($css, 'css')->textArea([
                            'rows' => 10,
                            'max-length' => 100000,
                        ])->label(false) ?>
                    </div>
                    <div class="tab-pane" id="preview">
                        <div class="row">
                            <iframe id="prev-frame" src="<?= yii::$app->urlManager->createUrl(['moqup/design-preview']) ?>" frameborder="0" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-pointer-lock" class="col-md-12"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['moqup/design-list'], ['class' => 'btn btn-secondary']) ?>
        
        <?php if (!$moqup->isNewRecord): ?>
            <?= Html::a(Yii::t('app', 'Delete'), '#', [
                'class' => 'btn btn-danger float-right',
                'onclick' => 'if (confirm("' . Yii::t('moqup', 'Are you sure you want to delete this moqup?') . '")) {
                    $.post("' . (Yii::$app->urlManager->createUrl(['moqup/design-delete/', 'id' => $moqup->id])) . '", {}, function(result) {
                        if (result == "1") {
                            location.href="' . (Yii::$app->urlManager->createUrl(['moqup/design-list', 'viewYours' => true])) . '";
                        }
                        else {
                            alert("' . Yii::t('moqup', 'Sorry, there was an error while trying to delete the moqup') . '");
                        }
                    });
                }',
            ]) ?>
        <?php endif; ?>
    </div>
</div>
<?php 
ActiveForm::end();

$this->registerjs('$("#toggle-prev").on("show.bs.tab", function() {
    var prevFrame = $("#prev-frame").contents();
    var prevCont = prevFrame.find("#prev-content");
    var prevStyle = prevFrame.find("#prev-style");
    
    var currentCont = $("#moqup-html").val();
    var currentStyle = $("#css-css").val();

    prevCont.html(currentCont);
    prevStyle.html(currentStyle);
})');

$this->registerjs('$("#toggle-prev").on("shown.bs.tab", function() {
    var prevHeight = $("#prev-frame").contents().height();
    $("#prev-frame").css("min-height", prevHeight + "px");
})');