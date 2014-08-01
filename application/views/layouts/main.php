<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            $assetUrl = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('application.views.assets'));
            $bootstrap = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('composer.twbs.bootstrap.dist'));
            $datepicker = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('composer.eternicode.bootstrap-datepicker'));
        ?>

        <meta charset="utf8" />
        <!-- Import Jquery -->
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

        <!-- Scale the UI dependant on device via TWBS -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- Import TWBS JS and CSS -->
        <script src="<?php echo $bootstrap; ?>/js/bootstrap.min.js"></script>
        <link href="<?php echo $bootstrap; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />

        <!-- Import custom utils -->
        <link  href="<?php echo $assetUrl; ?>/css/main.css" rel="stylesheet" type="text/css" media="all" />
        <script src="<?php echo $assetUrl; ?>/js/main.js"></script>

        <!-- Import Google Fonts -->
        <link  href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
        <link  href='http://fonts.googleapis.com/css?family=Raleway:400,300' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>

        <title>
            OSRS Market - Price Statistics and More!
        </title>

        <script>
            var baseUrl = '<?php echo Yii::app()->urlManager->baseUrl; ?>';
        </script>
    </head>
    <body>
        <div style="position: absolute; top:0; left:0; width:100%;">
            <div class="navigation">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="row">
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Rising</div>', array('/stats/rising')); ?>
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Falling</div>', array('/stats/falling')); ?>
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Merchant</div>', array('/stats/merchant')); ?>
                            </div>
                        </div>
                        <div class="col-sm-2 logo">
                            <center>
                                <?php echo CHtml::link(
                                CHtml::image($assetUrl . '/images/logo.png', 'Logo', array('class' => 'img-responsive')), 
                                Yii::app()->user->getReturnUrl(Yii::app()->homeUrl), 
                                array()); 
                                ?>
                            </center>
                        </div>
                        <div class="col-sm-5">
                            <div class="row">
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Pro</div>', array('/account/pro')); ?>
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Register</div>', array('/account/register')); ?>
                                <?php echo CHtml::link('<div class="col-sm-4 link hidden-xs">Login</div>', array('/login')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!($this->id == "home" && $this->action->id == "index")): ?>
                <?php if($this->breadcrumbs): ?>
                    <div class="breadcrumbs">
                        <div class="container">
                            <?php
                            $this->widget('zii.widgets.CBreadcrumbs', array(
                                'links' => $this->breadcrumbs,
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="container">
                    <br />
            <?php endif; ?>

            <?php if(Yii::app()->user->hasFlash("success")): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>Hell Yeah!</strong>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo Yii::app()->user->getFlash("success"); ?>
                </div>
            <?php endif; ?>

            <?php if(Yii::app()->user->hasFlash("danger")): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>Hold Up!</strong>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo Yii::app()->user->getFlash("danger"); ?>
                </div>
            <?php endif; ?>

            <?php if(Yii::app()->user->hasFlash("warning")): ?>
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>Wait a second!</strong>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo Yii::app()->user->getFlash("warning"); ?>
                </div>
            <?php endif; ?>

            <?php echo $content; ?>
            <div class="clear"></div>

            <?php if(!($this->id == "home" && $this->action->id == "index")): ?>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>