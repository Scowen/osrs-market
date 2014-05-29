<?php
$race = \application\components\CurrentRace::get();
?>

<?php if(isset($_GET['clear'])): ?>
    <?php 
    $bets = \application\models\db\Bets::model()->findAllByAttributes(array('race' => $race->id), array('order' => 'name ASC'));
    foreach($bets as $bet){
        $bet->delete();
    }

    Yii::app()->user->setFlash('success', 'All bets for the current race have been cleared!');
    ?>
<?php endif; ?>

<?php if(isset($_GET['win'])): ?>
    <?php
    $bets = \application\models\db\Bets::model()->findAllByAttributes(array('race' => $race->id), array('order' => 'name ASC'));
    $winners = \application\models\db\Bets::model()->findAllByAttributes(array('race' => $race->id, 'horse' => $_GET['win']));
    $race->winner = $_GET['win'];
    $race->save();
    ?>
    <h4>Pot: <strong>&pound;<?php echo count($bets) + $race->extra; ?></strong></h4>
    <?php if(!$winners || ($winners && count($winners) <= 0)): ?>
        <?php 
        $nextRace = \application\models\db\Races::model()->findByPk( ($race->id + 1) );
        if($nextRace){
            $nextRace->extra = count($bets) + $race->extra;
            $nextRace->save();
        }
        ?>
        <h4>No Winners this race, the pot has been carried over!</h4>
    <?php else: ?>
        <h4>Split: <strong>&pound;<?php echo number_format( (count($bets) + $race->extra) / count($winners), 2); ?></strong></h4>
        <h4>Winners:</h4>
        <table class="table table-condensed">
            <tbody>
                <?php foreach($winners as $bet): ?>
                    <tr>
                        <td><?php echo $bet->name; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php echo CHtml::link('Start Bets for Next Race', array('/home'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
<?php return; endif; ?>

<?php if(isset($_GET['add'])): ?>
    <?php
    $name = ucwords(strtolower($_GET['name']));
    $horse = $_GET['horse'];
    $quantity = $_GET['quantity'];

    $newBet = new \application\models\db\Bets;
    $newBet->name = $name;
    $newBet->horse = $horse;
    $newBet->quantity = $quantity;
    $newBet->race = $race->id;
    $newBet->created = time();
    $newBet->save();

    Yii::app()->user->setFlash('success', "<strong>Success!</strong> $name has placed a bet on horse #$horse.");
    ?>  
<?php endif; ?>



<?php if(Yii::app()->user->hasFlash('success')): ?>
<div class="row">
    <div class="col-sm-10" id="success">
        <div class="alert alert-success">
            <?php echo Yii::app()->user->getFlash('success');?>
        </div>
    </div>
</div>
<script>
$(document).ready( function(){
    window.history.pushState("object or string", "Title", baseUrl + "/home");
    setTimeout(function(){
        $('#success').fadeOut();
    }, 4000);
})
</script>
<?php endif; ?>


<div id="formBetArea" class="row">
    <div class="col-sm-8">
        <input type="text" name="playername" autofocus="true" placeholder="Enter a Name..." id="inputPlayerName" class="form-control input-lg pop" data-trigger="focus" data-placement="top" data-toggle="popover" data-html="true" data-content="The person making the bet" role="input">
    </div>
    <div class="col-sm-2">
        <input type="text" name="horse" placeholder="#" id="inputHorse" class="form-control input-lg text-center pop" data-trigger="focus" data-placement="top" data-toggle="popover" data-html="true" data-content="The Horse Number" role="input">
    </div>

    <div class="col-sm-2 hidden">
        <select name="quantity" id="inputQuantity" class="form-control input-lg text-center pop" data-trigger="focus" data-placement="top" data-toggle="popover" data-html="true" data-content="Number of tickets" role="input">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>
    <div class="col-sm-2">
        <?php echo CHtml::link('Place Bet', '', array('id' => 'linkPlace', 'class' => 'btn btn-lg btn-primary')); ?>
    </div>

    <script>
    $(document).ready( function(){
        var baseUrl = '<?php echo Yii::app()->urlManager->baseUrl; ?>';

        $('#inputPlayerName').bind("enterKey",function(e){
            update();
        });

        $("#divBetArea").hide();

        $("#linkPlace").click( function(){
            update();
        })

        function update(){
            var betName = $("#inputPlayerName").val();
            var betHorse = $("#inputHorse").val();
            var betQuan = $("#inputQuantity").val();

            if(betName != "" && betHorse != "" && betQuan >= 1){
                // $("#divBetArea").load( baseUrl + '?add&name=' + betName + '&horse=' + betHorse + '&quantity=' + betQuan);
                $("#formBetArea").attr("class","row has-success");
                $("#divBetArea").fadeIn();
                window.location.href = baseUrl + '/home?add&name=' + betName + '&horse=' + betHorse + '&quantity=' + betQuan;
            } else {
                $("#divBetArea").html('<div class="alert alert-danger">There was an error processing the bet, please make sure all fields are filled in.</div>');
                $("#formBetArea").attr("class","row has-error");
                $("#divBetArea").fadeIn();
            }
        }
    })
    </script>
</div>

<br />

<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                Bets on Horses
            </div>
            <table class="table table-condensed table-hover">
                <thead>
                    <th>Horse</th>
                    <th>N.O Bets</th>
                </thead>
                <tbody>
                    <?php 
                    $sql = 'SELECT * FROM `bets` WHERE `bets`.`race` = :race GROUP BY `bets`.`horse` ORDER BY `bets`.`horse`';
                    $bets = \application\models\db\Bets::model()->findAllBySql($sql, array(':race' => $race->id));

                    foreach($bets as $bet){
                        $betsOnHorse = \application\models\db\Bets::model()->findAllByAttributes(array('horse' => $bet->horse, 'race' => $race->id));
                        ?>
                        <tr>
                            <td>#<?php echo $bet->horse; ?></td>
                            <td><?php echo count($betsOnHorse); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Bets for this Race
            </div>
            <table id="bets" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Horse</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $bets = \application\models\db\Bets::model()->findAllByAttributes(array('race' => $race->id), array('order' => 'name ASC')); ?>
                    <?php foreach($bets as $bet): ?>
                        <tr>
                            <td><?php echo $bet->name ?></td>
                            <td>#<?php echo $bet->horse; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                Current Race Statistics
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">Pot:</div>
                    <div id="pot" class="col-sm-6 text-right"><strong>&pound;<?php echo count($bets) + $race->extra ?></strong></div>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">Race:</div>
                    <div class="col-sm-6 text-right"><strong><?php echo $race->name; ?></strong></div>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">Start:</div>
                    <div class="col-sm-6 text-right"><strong><?php echo Yii::app()->dateFormatter->formatDateTime($race->start, null, 'short'); ?></strong></div>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">End:</div>
                    <div class="col-sm-6 text-right"><strong><?php echo Yii::app()->dateFormatter->formatDateTime($race->end, null, 'short'); ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>