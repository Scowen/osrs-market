<?php
    /**
     * @var LogoutController    $this
     * @var CForm               $form
     */
    $this->pageTitle = Yii::t('application', 'Logout');
    $this->breadcrumbs = array(
        $this->pageTitle,
    );
    // The errorContainerTag options cannot be set through CActiveForm, so set it mnaually here.
    CHtml::$errorContainerTag = 'span';
?>
<h1><?php echo $this->pageTitle; ?></h1>

<div class="form">
    <?php echo $form->render(); ?>
</div>
