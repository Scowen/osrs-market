<?php
    $this->pageTitle = Yii::t('application', 'Register');
    $this->breadcrumbs = array(
        $this->pageTitle,
    );
?>

<?php if(Yii::app()->user->isGuest): ?>
    <div class="row">
        <div class="col-sm-6 text-center">
            <br /><br /><br /><br /><br /><br /><br />Put some benefits to registering here.
        </div>
        <div class="col-sm-6">
            <div class="col-sm-offset-1 page-header">
                <h1>Register</h1>
            </div>
            <?php
            $form->attributes = array('class' => 'form-horizontal');
            echo $form->renderBegin();
            $widget = $form->activeFormWidget;
            ?>
            <div class="form-group <?php echo $widget->error($form, 'username') ? 'has-error' : ''; ?>">
                <?php echo $widget->labelEx($form, 'username', array('class' => 'col-xs-12 col-sm-4 control-label')); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo $widget->input($form, 'username', array('class' => 'form-control', 'autofocus' => 'true')); ?>
                    <?php echo $widget->error($form, 'username', array('class' => 'help-block')) ?: ''; ?>
                </div>
            </div>

            <div class="form-group <?php echo $widget->error($form, 'password') ? 'has-error' : ''; ?>">
                <?php echo $widget->labelEx($form, 'password', array('class' => 'col-xs-12 col-sm-4 control-label')); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo $widget->input($form, 'password', array('class' => 'form-control')); ?>
                    <?php echo $widget->error($form, 'password', array('class' => 'help-block')) ?: ''; ?>
                </div>
            </div>

            <div class="form-group <?php echo $widget->error($form, 'email') ? 'has-error' : ''; ?>">
                <?php echo $widget->labelEx($form, 'email', array('class' => 'col-xs-12 col-sm-4 control-label')); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo $widget->input($form, 'email', array('class' => 'form-control')); ?>
                    <?php echo $widget->error($form, 'email', array('class' => 'help-block')) ?: ''; ?>
                </div>
            </div>

            <div class="col-sm-offset-4">
                <?php echo $widget->button($form, 'submit', array('class' => 'btn btn-primary')); ?>
            </div>
            <?php echo $form->renderEnd(); ?>
        </div>
    </div>
<?php else: ?>
    <div class="jumbotron">
        <div class="container">
            <h1>Again? Why?</h1>
            <p>You want to register again? But you're already logged in!</p>
        </div>
    </div>
<?php endif; ?>