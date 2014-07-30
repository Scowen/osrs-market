<?php 
$assetUrl = Yii::app()->assetPublisher->publish(Yii::getPathOfAlias('application.views.assets'));
?>

<div style="width:100%; height:300px; overflow:hidden; z-index:100;" id="image-cycle" class="carousel slide hidden-xs" data-ride="carousel" data-interval="4000" data-wrap="true">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#image-cycle" data-slide-to="0" class="active"></li>
        <li data-target="#image-cycle" data-slide-to="1"></li>
        <li data-target="#image-cycle" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">

        <div class="item active">
            <img src="<?php echo $assetUrl; ?>/images/carousel-brown-rise.png" class="img-responsive" style="height:100%">
            <div class="carousel-caption">
                <div class="carousel-text-free">
                    <div class="font-opensans text-extra-large text-shadow-dark text-left">
                        ON THE RISE
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-xs-2 text-center">
                            <img src="#" class="img-responsive" alt="Item" width="60px" height="60px">
                        </div>
                        <div class="col-xs-7">
                            <div class="font-opensans text-large text-left">
                                Abyssal Whip
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <span class="font-opensans text-large">+ 1.4m</span>
                        </div>
                    </div>
                </div>
                <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            </div>
        </div>

        <div class="item">
            <img src="<?php echo $assetUrl; ?>/images/carousel-brown-fall.png" style="height:100%">
            <div class="carousel-caption">
                <div class="carousel-text-free">
                    <div class="font-opensans text-extra-large text-shadow-dark text-right">
                        GOING DOWN
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-xs-3">
                            <span class="font-opensans text-large">- 2.1m</span>
                        </div>
                        <div class="col-xs-7">
                            <div class="font-opensans text-large text-right">
                                Bandos Godsword
                            </div>
                        </div>
                        <div class="col-xs-2 text-center">
                            <img src="#" class="img-responsive" alt="Item" width="60px" height="60px">
                        </div>
                    </div>
                </div>
                <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            </div>
        </div>

        <div class="item">
            <img src="<?php echo $assetUrl; ?>/images/carousel-brown.png" style="height:100%">
            <div class="carousel-caption">
                <div class="carousel-text-free">

                    <div class="font-opensans text-extra-large text-shadow-dark text-center">
                        <div class="item-firecape"></div>
                        POPULAR
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-xs-3">
                            <span class="font-opensans text-large">3125 Offers</span>
                        </div>
                        <div class="col-xs-7">
                            <div class="font-opensans text-large text-right">
                                Shark
                            </div>
                        </div>
                        <div class="col-xs-2 text-center">
                            <img src="#" class="img-responsive" alt="Item" width="60px" height="60px">
                        </div>
                    </div>
                </div>
                <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            </div>
        </div>
    </div>

    <!-- Controls -->
    <a class="left carousel-control" href="#image-cycle" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#image-cycle" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
</div>

<div class="container">
    <div class="row font-opensans">
        <div class="col-sm-6 col-xs-12 bg-light-green">
            <div class="row">
                <div class="col-xs-12 text-center font-opensans text-large">Rising</div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Abyssal Whip
                </div>
                <div class="col-xs-2 text-medium">
                    +1.4m
                </div>
            </div>
            <div class="separate-row"></div>
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Amulet of Fury
                </div>
                <div class="col-xs-2 text-medium">
                    +0.8m
                </div>
            </div>
            <div class="separate-row"></div>
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Bandos Chest Plate
                </div>
                <div class="col-xs-2 text-medium">
                    +0.5m
                </div>
            </div>
            <div class="separate-row"></div>
        </div>
    
        <div class="col-sm-6 col-xs-12 bg-light-red">
            <div class="row">
                <div class="col-xs-12 text-center font-opensans text-large">Falling</div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Bandos Godsword
                </div>
                <div class="col-xs-2 text-medium">
                    -2.1m
                </div>
            </div>
            <div class="separate-row"></div>
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Saradomin Sword
                </div>
                <div class="col-xs-2 text-medium">
                    -1.3m
                </div>
            </div>
            <div class="separate-row"></div>
            <div class="row">
                <div class="col-xs-2">
                    <img src="#" class="img-responsive" alt="Item" width="40px" height="40px">
                </div>
                <div class="col-xs-8 text-medium">
                    Bandos Boots
                </div>
                <div class="col-xs-2 text-medium">
                    -0.4m
                </div>
            </div>
            <div class="separate-row"></div>
        </div>
    </div>





</div>