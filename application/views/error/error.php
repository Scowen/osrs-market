<?php
    /**
     * @var ErrorController $this
     * @var array $error
     */
    $this->pageTitle = Yii::t('application', 'Error');
    $this->breadcrumbs = null;
?>
<h1><?php echo $this->pageTitle . ' 404'; ?></h1>

<div class="error">
    <p><?php echo Yii::t('application', 'You\'ve stumbled upon the error page! But seeing as there isn\'t actually an error, we hope you have a nice day instead!'); ?></p>
</div>
