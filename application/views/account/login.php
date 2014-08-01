<?php
    /**
     * @var LoginController $this
     * @var CForm           $form
     */
    $this->pageTitle = Yii::t('application', 'Login');
    $this->breadcrumbs = array(
        $this->pageTitle,
    );
?>

<?php
    $form->attributes = array('class' => 'form-horizontal');
    echo $form->renderBegin();
    $widget = $form->activeFormWidget;
?>
<fieldset>

    <?php if(isset($form->title) && is_string($form->title) && strlen($form->title)): ?>
        <legend><?php echo CHtml::encode($form->title); ?></legend>
    <?php endif; ?>

    <div class="form-group <?php echo $widget->error($form, 'username') ? 'has-error' : ''; ?>">
        <?php echo $widget->labelEx($form, 'username', array('class' => 'col-xs-12 col-sm-2 control-label')); ?>
        <div class="col-xs-12 col-sm-6">
            <?php echo $widget->input($form, 'username', array('class' => 'form-control', 'autofocus' => 'true')); ?>
            <?php
                echo $widget->error($form, 'username', array('class' => 'help-block'))
                  ?: $widget->hint($form, 'username', 'div', array('class' => 'help-block'));
            ?>
        </div>
    </div>

    <div class="form-group <?php echo $widget->error($form, 'password') ? 'has-error' : ''; ?>">
        <?php echo $widget->labelEx($form, 'password', array('class' => 'col-xs-12 col-sm-2 control-label')); ?>
        <div class="col-xs-12 col-sm-6">
            <?php echo $widget->input($form, 'password', array('class' => 'form-control')); ?>
            <?php
                echo $widget->error($form, 'password', array('class' => 'help-block'))
                  ?: $widget->hint($form, 'password', 'div', array('class' => 'help-block'));
            ?>
        </div>
    </div>

    <div class="col-sm-offset-2">
        <?php echo $widget->button($form, 'submit', array('class' => 'btn btn-primary')); ?>
    </div>

</fieldset>
<?php echo $form->renderEnd(); ?>
