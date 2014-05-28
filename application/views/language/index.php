<p>
    <?php
        switch($errorCode) {
            case $this::SUCCESS:
                echo Yii::t('application', 'Successfully changed the application language.');
                break;
            case $this::ERROR_NO_LANGUAGE:
                echo Yii::t('application', 'This page is for changing the application language, but no language has been specified. Please select from the following:');
                break;
            case $this::ERROR_INVALID_LANGUAGE:
                echo Yii::t('application', 'The language you specified does not exist within the application. Please select from the following:');
                break;
            default:
                echo Yii::t('application', 'An unknown error occured whilst trying to change the application language.');
                break;
        }
    ?>
</p>

List of languages available here...
