<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8" />
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <?php
            $assetUrl = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('application.views.assets'));
            $bootstrap = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('composer.twbs.bootstrap.dist'));
            $datepicker = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('composer.eternicode.bootstrap-datepicker'));
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $bootstrap; ?>/css/bootstrap.min.css" media="all" />
        <!-- <link rel="stylesheet" type="text/css" href="<?php // echo Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('themes.classic.assets') . '/css/styles.css'); ?>" media="all" /> -->
        <script src="https://code.jquery.com/jquery.js"></script>
        <script src="<?php echo $bootstrap; ?>/js/bootstrap.min.js"></script>
        <link href="<?php echo $bootstrap; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />

        <script src="<?php echo $datepicker; ?>/js/bootstrap-datepicker.js"></script>
        <link href="<?php echo $datepicker; ?>/css/datepicker.css" rel="stylesheet" type="text/css" media="all" />
        <title>
            Herbert Racing 2014
        </title>

        <script>
            var baseUrl = '<?php echo Yii::app()->urlManager->baseUrl; ?>';

            $(document).ready( function(){
                // Load the basic datepicker
                $(".input-group .date").datepicker({ autoclose: true, todayHighlight: true });
                // Load a view that looks at the years
                $(".date-year").datepicker({ autoclose: true, todayHighlight: false, startView: "decade" });
                // Load a view that looks at the months
                $(".date-month").datepicker({ autoclose: true, todayHighlight: false, startView: "year" });
            })
        </script>

        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <style type="text-css">

        </style>
    </head>

    <body>

        <?php $this->renderPartial('//modals/winner'); ?>

        <div class="row">
            <div class="col-sm-4" style="margin-top:5px; margin-left:5px;">
                <!-- Single button -->
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-md dropdown-toggle" data-toggle="dropdown">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#winner" data-toggle="modal"><i class="glyphicon glyphicon-gift"></i> <div class="pull-right">Find Winners</div></a></li>
                        <li class="divider"></li>
                        <li><?php echo CHtml::link('<i class="glyphicon glyphicon-trash"></i> <div class="pull-right">Clear Bets</div>', array('/home?clear'), array()); ?></li>
                    </ul>
                </div>

                <?php
                $races = '<table class="table table-condensed"><tr><th>Name</th> <th>Time</th></tr>';
                foreach(\application\models\db\Races::model()->findAll() as $race){
                    if(time() > $race->start && time() < $race->end)
                        $races .= '<tr class="success text-success">';
                    else $races .= '<tr>';

                        $races .= '<td>'.$race->name.'</td>';
                        $races .= '<td>';

                            $races .= Yii::app()->dateFormatter->formatDateTime($race->start, null, 'short');
                            if($race->end)
                                $races .= ' <i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::app()->dateFormatter->formatDateTime($race->end, null, 'short');
                        $races .= '</td>';
                    $races .= '</tr>';
                }
                $races .= '</table>';
                ?>
                <?php echo CHtml::link('Races', '', array('class' => 'btn btn-md btn-primary pop', 'data-trigger' => 'hover', 'data-placement' => 'bottom', 'data-toggle' => 'popover', 'data-html' => 'true', 'data-content' => $races, 'role' => 'button', 'style' => 'cursor:pointer;')); ?>
            </div>
            <div class="col-sm-3 text-center">
                <?php echo CHtml::image($assetUrl . '/images/banner.png', 'Herbert Racing', array( )); ?>
            </div>

            <div class="col-sm-4 pull-right" style="margin-top:15px">
                <?php $currentRace = \application\components\CurrentRace::get(); ?>
                Taking bets for: <strong><?php echo CHtml::encode($currentRace->name); ?></strong>
                (<?php echo Yii::app()->dateFormatter->formatDateTime($currentRace->start, null, 'short'); ?>
                <?php if($currentRace->end) echo ' <i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::app()->dateFormatter->formatDateTime($currentRace->end, null, 'short'); ?>)

                <?php if(!$currentRace->winner && time() > $currentRace->end): ?>
                    <br /><span class="bg-warning text-warning"><strong>This race is over, determine a winner!</strong></span>
                <?php endif; ?>
            </div>

            <script>
            $(document).ready( function(){
                $('.pop').popover('hide');
            });
            </script>
        </div>

        <div class="container" id="page">
            <br /><br />
            <?php echo $content; ?>
            <div class="clear"></div>
        </div>

    </body>
</html>

<?php return; ?>


<?php /* @var $this Controller  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <!-- Bootstrap CSS framework -->
    <?php
        $bootstrap = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('composer.twbs.bootstrap.dist'));
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $bootstrap; ?>/css/bootstrap.css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('themes.classic.assets') . '/css/styles.css'); ?>" media="all" />
    <style type="text/css">
        #mainmenu {
            background: #fff url("<?php echo Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('themes') . '/classic/assets/images/bg.gif'); ?>") repeat-x left top;
        }
    </style>

    <title>DEV-VIEWS: <?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

    <div id="header">
        <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
    </div><!-- header -->

    <div id="mainmenu">
        <?php $this->widget('zii.widgets.CMenu',array(
            'items'=>array(
                array('label'=>Yii::t('application', 'Home'), 'url'=>Yii::app()->homeUrl),
                array('label'=>Yii::t('application', 'Login'), 'url'=>array('/login'), 'visible'=>Yii::app()->user->isGuest),
                array('label'=>Yii::t('application', 'Logout ({name})', array('{name}' => Yii::app()->user->displayName)), 'url'=>array('/logout'), 'visible'=>!Yii::app()->user->isGuest),
            ),
        )); ?>
    </div><!-- mainmenu -->
    <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
    <?php endif?>

    <?php echo $content; ?>

    <div class="clear"></div>

    <div id="footer">
        <?php
            echo Yii::t(
                'application',
                'Copyright &copy; {year} by {company}.',
                array(
                    '{year}' => date('Y'),
                    '{company}' => Yii::app()->name,
                )
            );
        ?>
        <?php
            echo Yii::t('application', 'All rights reserved.');
        ?>
        <br />
        <?php
            $languages = array(
                'en' => 'English',
                'cy' => 'Cymraeg',
            );
            foreach($languages as $code => &$lang) {
                $lang = CHtml::link($lang, array('/language', 'lang' => $code));
            }
            echo implode(' &middot; ', $languages);
        ?>
        <br />
        <?php echo Yii::powered(); ?>
    </div><!-- footer -->

</div><!-- page -->

</body>
</html>
*/
