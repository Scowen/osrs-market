<div class="container">
    <?php if($zybez): ?>
        <div class="alert alert-success">
            <strong>Success!</strong>
            <br />
            <pre class="pre">
<?php var_dump($zybez); ?>
            </pre>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <strong>Oh Shit!</strong>
            <br />
            <pre class="pre">
<?php var_dump($zybez); ?>
            </pre>
        </div>
    <?php endif; ?>
</div>