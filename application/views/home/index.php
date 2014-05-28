<?php
$race = 1;
?>
<?php if(isset($_GET['add'])): ?>
    <?php
    $name = ucwords(strtolower($_GET['name']));
    $horse = $_GET['horse'];
    $quantity = $_GET['quantity'];

    $newBet = new \application\models\db\Bets;
    $newBet->name = $name;
    $newBet->horse = $horse;
    $newBet->quantity = $quantity;
    $newBet->race = $race;
    $newBet->created = time();
    $newBet->save();
    ?>
    <div class="alert alert-success">
        <strong>Success!</strong> <?php echo $name; ?> has placed a bet on horse #<?php echo $horse; ?> for &pound;<?php echo $quantity; ?>.00!
    </div>
<?php return; endif; ?>
<div class="col-sm-10">
    <div id="divBetArea"></div>
</div>

<div id="formBetArea" class="form-group">
    <div class="col-sm-6">
        <input type="text" name="name" placeholder="Enter a Name..." id="inputName" class="form-control input-lg pop" data-trigger="focus" data-placement="top" data-toggle="popover" data-html="true" data-content="The person making the bet" role="input">
    </div>
    <div class="col-sm-2">
        <input type="text" name="horse" placeholder="#" id="inputHorse" class="form-control input-lg text-center pop" data-trigger="focus" data-placement="top" data-toggle="popover" data-html="true" data-content="The Horse Number" role="input">
    </div>

    <div class="col-sm-2">
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

        $("#divBetArea").hide();

        $("#linkPlace").click( function(){
            var betName = $("#inputName").val();
            var betHorse = $("#inputHorse").val();
            var betQuan = $("#inputQuantity").val();

            if(betName != "" && betHorse != "" && betQuan >= 1){
                $("#divBetArea").load( baseUrl + '?add&name=' + betName + '&horse=' + betHorse + '&quantity=' + betQuan);
                $("#formBetArea").attr("class","form-group has-success");
                $("#divBetArea").fadeIn();
            } else {
                $("#divBetArea").html('<div class="alert alert-danger">There was an error processing the bet, please make sure all fields are filled in.</div>');
                $("#formBetArea").attr("class","form-group has-error");
                $("#divBetArea").fadeIn();
            }
        })
    })
    </script>
</div>
<?php /*
<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Top Five Bets for this Race</h3>
        </div>
        <table class="table table-condensed table-hover">
            <thead>
                <th>Horse</th>
                <th>N.O Bets</th>
                <th>Pot</th>
            </thead>
            <tbody>
                <?php // foreach(\application\models\db\Bets::model()->findAllByAttributes('race' => $race) as $bet): ?>
                    <tr>
                        <td># <?php // echo $bet->horse; ?></td>
                        <td><?php // echo $bet-> UNFINISHED
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
*/ ?>