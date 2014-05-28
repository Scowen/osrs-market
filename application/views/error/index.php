<?php
    /**
     * @var ErrorController $this
     * @var array $error
     */
    $this->pageTitle = Yii::t('application', 'Error');
    $this->breadcrumbs = null;
?>
<h1><?php echo $this->pageTitle . ' ' . $code; ?></h1>

<div class="error">
    <p><?php echo CHtml::encode($message); ?></p>
</div>
