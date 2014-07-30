<?php
// Include the header page which includes the main layout CSS, JS etc.
include("header.php");
?>

<div style="width:100%; height:300px; overflow:hidden; z-index:100;" id="image-cycle" class="carousel slide" data-ride="carousel" data-interval="4000" data-wrap="true">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#image-cycle" data-slide-to="0" class="active"></li>
        <li data-target="#image-cycle" data-slide-to="1"></li>
        <li data-target="#image-cycle" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">

        <div class="item active">
            <img src="assets/images/carousel-brown-rise.png" class="img-responsive" style="height:100%">
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
                    </div>
                </div>
                <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            </div>
        </div>

        <div class="item">
            <img src="assets/images/carousel-brown.png" style="height:100%">
            <div class="carousel-caption">
                <div class="carousel-text-free">
                    Testing
                </div>
                <br />
            </div>
        </div>

        <div class="item">
            <img src="assets/images/carousel-brown.png" style="height:100%">
            <div class="carousel-caption">
                <div class="carousel-text-free">
                    Testing
                </div>
                <br />
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

<?php
// Include the footer to end main html tags.
include("footer.php");
