<div class="modal fade" id="winner" tabindex="-1" role="dialog" aria-labelledby="winnerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">

                <div id="divWin">
                    <h2>Find out who won!</h2>
                    <div class="col-sm-4 col-sm-offset-3 text-center">
                        <input type="text" placeholder="Horse #" name="win" id="inputWin" class="form-control input-lg text-center">
                    </div>
                    <div class="col-sm-2">
                        <?php echo CHtml::link('Go', '', array('class' => 'btn btn-lg btn-primary', 'id' => 'linkGo')); ?>
                    </div>

                    <br /><br /><br /><br />
                </div>

                <script>
                $(document).ready( function(){
                    $("#linkGo").click( function(){
                        var horse = $("#inputWin").val();
                        $("#divWin").load( baseUrl + '/home?win=' + horse);
                    })
                })
                </script>



            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
$(document).ready( function(){
    $('#winner').modal({
        show: false,
        backdrop: 'static',
        keyboard: false,
    })
})
</script>