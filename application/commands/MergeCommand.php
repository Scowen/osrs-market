<?php

    use \Yii;
    use \CException as Exception;

    /**
     * Merge Command
     *
     * The merge command managed to merging of old to new database contents, after
     * migrations have created the required schema.
     *
     * @author Zander Baldwin <mynameis@zande.rs>
     * @package application.commands
     * @since 1.1.6
     */
    class MergeCommand extends \CConsoleCommand
    {

        const BASE_MERGE='m000000_000000_base';

        /**
         * @access public
         * @var string $mergePath
         * The directory that stores the migrations. This must be specified in terms of a path alias, and the
         * corresponding directory must exist.
         */
        public $mergePath = 'application.migrations.merges';

        /**
         * @access public
         * @var string $templateFile
         * The path of the template file for generating new migrations. This must be specified in terms of a path alias
         * (for example, "application.migrations.template"). If this property is not set, then an internal template will
         * be used.
         */
        public $templateFile;

        /**
         * @access public
         * @var string $mergeTable
         * The name of the table for keeping applied merge information. This table will be automatically created if it
         * does not exist. The table structure is: merge name (primary key), from (string), to (string) apply time
         * (integer).
         */
        public $mergeTable = '{{merge}}';

        /**
         * @access public
         * @var string $defaultAction
         */
        public $defaultAction = 'run';

        /**
         * @access public
         * @var boolean $interactive
         */
        public $interactive = true;

        /**
         * @access protected
         * @var array<mixed> $from
         */
        protected $from = array();

        /**
         * Filter: Before Action
         *
         * @access public
         * @param string $action
         * @param array $params
         * @return boolean
         */
        public function beforeAction($action, $params)
        {
            $path = Yii::getPathOfAlias($this->mergePath);
            if($path === false || !is_dir($path)) {
                echo 'Error: The merge directory does not exist: ' . $this->mergePath . "\n";
                exit(1);
            }
            $this->mergePath = $path;
            echo "\n" . 'System62 Merge Tool v1.0 (based on Yii v' . Yii::getVersion() . ")\n\n";
            return parent::beforeAction($action, $params);
        }

        /**
         * Interactive Confirmation
         *
         * @access public
         * @param string $message
         * @param boolean $default
         * @return boolean
         */
        public function confirm($message, $default = false)
        {
            return $this->interactive
                ? parent::confirm($message, $default)
                : true;
        }

        /**
         * Create Database Connections
         *
         * @access protected
         * @param string $fromEnvironment
         * @param string $toEnvironment
         * @return void
         */
        protected function createDbConnections($fromEnvironment, $toEnvironment)
        {
            if(!preg_match(VALIDLABEL, $fromEnvironment) || !preg_match(VALIDLABEL, $toEnvironment)) {
                echo 'Error: Specify valid to and from environments.' . "\n";
                exit(1);
            }
            $databases = require Yii::getPathOfAlias('application.config.databases') . '.php';
            if(!isset($databases[$fromEnvironment]) || !isset($databases[$toEnvironment])) {
                echo 'Error: Database credentials must exist for each environment.' . "\n";
                exit(1);
            }
            $toProperties = CMap::mergeArray(get_object_vars(Yii::app()->db), $databases[$toEnvironment]);
            $toConnection = new CDbConnection;
            foreach($toProperties as $property => $value) {
                $toConnection->$property = $value;
            }
            $fromProperties = CMap::mergeArray(get_object_vars(Yii::app()->db), $databases[$fromEnvironment]);
            $fromConnection = new CDbConnection;
            foreach($fromProperties as $property => $value) {
                $fromConnection->$property = $value;
            }
            try {
                $toConnection->active = $fromConnection->active = true;
            }
            catch(Exception $e) {
                echo 'Error: Could not establish database connection.' . "\n";
            }
            $this->from['environment'] = $fromEnvironment;
            $this->from['connection'] = $fromConnection;

            // Finally, for ActiveRecord models and migration methods to work correctly, set the application-wide
            // database connection to the "to" connection object.
            Yii::app()->setComponent('db', $toConnection);
        }

        /**
         * Get: From Environment
         *
         * @access public
         * @return string
         */
        protected function getFromEnvironment()
        {
            return $this->from['environment'];
        }

        /**
         * Get: From Database Connection
         *
         * @access public
         * @return CDbConncection
         */
        protected function getFromDbConnection()
        {
            return $this->from['connection'];
        }

        /**
         * Instantiate Migration
         *
         * @access protected
         * @param string $class
         * @return MergeInterface
         */
        protected function instantiateMerge($class)
        {
            $file = $this->mergePath . "/{$class}.php";
            require_once($file);
            $merge = new $class;
            if($merge instanceof MergeInterface) {
                $merge->setFrom($this->getFromEnvironment(), $this->getFromDbConnection());
                return $merge;
            }
        }

        /**
         * Run Merge
         *
         * @access protected
         * @return boolean
         */
        protected function runMerge($class)
        {
            if($class === self::BASE_MERGE) {
                return true;
            }
            echo "*** Applying {$class}\n";
            $start = microtime(true);
            $merge = $this->instantiateMerge($class);
            if(is_object($merge) && $merge->run() !== false) {
                Yii::app()->db->createCommand()->insert($this->mergeTable, array(
                    'merge'         => $class,
                    'from'          => $this->getFromEnvironment(),
                    'apply_time'    => time(),
                ));
                $time = microtime(true)-$start;
                echo "*** Applied {$class} (time: " . sprintf("%.3f", $time) . "s)\n\n";
                return true;
            }
            else {
                $time = microtime(true) - $start;
                echo "*** Failed to apply {$class} (time: " . sprintf("%.3f", $time) . "s)\n\n";
                return false;
            }
        }

        /**
         * Get: Merge History
         *
         * @access protected
         * @param integer $limit
         * @return array
         */
        protected function getMergeHistory($limit)
        {
            if(Yii::app()->db->schema->getTable($this->mergeTable, true) === null) {
                $this->createMergeHistoryTable();
            }
            return CHtml::listData(
                Yii::app()->db->createCommand()
                    ->select('merge, apply_time')
                    ->from($this->mergeTable)
                    ->where(array('and', Yii::app()->db->quoteColumnName('from') . ' = :from', Yii::app()->db->quoteColumnName('merge') . ' != :base'))
                    ->order('merge')
                    ->limit($limit)
                    ->bindValue(':from', $this->getFromEnvironment())
                    ->bindValue(':base', self::BASE_MERGE)
                    ->queryAll(),
                'merge',
                'apply_time'
            );
        }

        /**
         * Create Merge History Table
         *
         * @access protected
         * @return void
         */
        protected function createMergeHistoryTable()
        {
            echo 'Creating migration history table "' . $this->mergeTable . '"...';
            Yii::app()->db->createCommand()->createTable($this->mergeTable, array(
                'merge'         => 'VARCHAR(180) NOT NULL',
                'from'          => 'VARCHAR(64) NOT NULL',
                'apply_time'    => 'INT NOT NULL'
            ));
            Yii::app()->db->createCommand()->addPrimaryKey('merge_pk', $this->mergeTable, 'merge, from');
            /*
                Yii::app()->db->createCommand()->insert($this->mergeTable, array(
                    'merge'         => self::BASE_MERGE,
                    'from'          => $this->getFromEnvironment(),
                    'apply_time'    => time(),
                ));
            /**/
            echo "Done.\n";
        }

        /**
         * Get: Help
         *
         * @access public
         * @return string
         */
    public function getHelp()
    {
        return <<<EOD
USAGE
  yiic merge [action] [parameter]

DESCRIPTION
  This command provides support for the merging of content between two
  databases. The optional 'action' parameter specifies which specific
  merge task to perform. It can take these values: create, history, new,
  run. If the 'action' parameter is not given, it defaults to 'run'.
  Each action takes different parameters. Their usage can be found in
  the following examples.

EXAMPLES
 * yiic merge
   Applies ALL new merges. This is equivalent to 'yiic merge run'.

 * yiic merge create name_of_merge
   Creates a new merge named "name_of_merge". The merge name must
   contain only letters, numbers and underscores.

 * yiic migrate history [n]
   Shows the last "n" previously applied merges. If the "n" parameter is
   ommitted, then ALL previously applied merge information is shown.

 * yiic migrate new [n]
   Shows the next "n" merges that have not been applied. If the "n"
   parameter is ommitted, then ALL new merges are shown.

 * yiic merge run [n] --to=<environment> --from=<environment>
   Apply the next "n" new merges. If the "n" parameter is ommitted, then
   ALL new merges are applied.
   This will merge content from the "from" database to the "to"
   database, where "to" and "from" are valid environments with
   corresponding database credentials in the configuration.

EOD;
    }

        /**
         * Get: New Merges
         *
         * @access public
         * @return array
         */
        protected function getNewMerges()
        {
            $applied = array();
            foreach($this->getMergeHistory(-1) as $merge => $time) {
                $applied[substr($merge, 1, 13)] = true;
            }
            $merges = array();
            $handle = opendir($this->mergePath);
            while(($file = readdir($handle)) !== false) {
                if($file === '.' || $file === '..') {
                    continue;
                }
                $path = $this->mergePath . DIRECTORY_SEPARATOR . $file;
                if(preg_match('/^(m(\\d{6}_\\d{6})_.*?)\\.php$/', $file, $matches) && is_file($path) && !isset($applied[$matches[2]])) {
                    $merges[] = $matches[1];
                }
            }
            closedir($handle);
            sort($merges);
            return $merges;
        }

        /**
         * Get: Template
         *
         * @access protected
         * @return string
         */
        protected function getTemplate()
        {
            return $this->templateFile !== null
                ? file_get_contents(Yii::getPathOfAlias($this->templateFile) . '.php')
                : <<<EOD
<?php

    class {ClassName} extends BaseMerge
    {

        /**
         * Run Merge
         *
         * @access public
         * @return void
         */
        public function run()
        {
        }

    }
EOD;
    }

        /**
         * Action: Index
         *
         * @access public
         * @param string $from
         * @param string $to
         * @return void
         */
        public function actionRun($args, $from, $to)
        {
            $this->createDbConnections($from, $to);
            if(empty($merges = $this->getNewMerges())) {
                echo "No new merges found. Your system is up-to-date.\n";
                return 0;
            }
            $total = count($merges);
            $step = isset($args[0]) && preg_match('/^[1-9]\\d*$/') ? (int) $args[0] : 0;
            if($step > 0) {
                $merges = array_slice($merges, 0, $step);
            }
            $n = count($merges);
            echo $n === $total
                ? Yii::t('app', "Total of {n} new merge to be applied:\n|Total of {n} new merges to be applied:\n", array($n))
                : Yii::t('app', "Total of {n} out of {total} merges to be applied:\n", array($n, 'total' => $total));

            foreach($merges as $mergeName) {
                echo "    {$mergeName}\n";
            }
            echo "\n";
            if($this->confirm(Yii::t('app', "Apply the above merge?|Apply the above merges?", array($n)))){
                foreach($merges as $mergeName) {
                    if($this->runMerge($mergeName) === false) {
                        echo "Error: Merge failed. All remaining merges have been cancelled.\n";
                        exit(1);
                    }
                }
                echo "\nMerges completed successfully.\n";
            }
        }

        /**
         * Action: Create
         *
         * @access public
         * @param array<string> $args
         * @return void
         */
        public function actionCreate($args)
        {
            if(!isset($args[0])) {
                $this->usageError('Please provide the name of the new merge.');
            }
            elseif(!preg_match('/^\\w+$/', $args[0])) {
                $this->usageError('The name of the merge contains invalid characters.');
            }
            $name = 'm' . gmdate('ymd_His') . '_' . $args[0];
            $content = strtr($this->getTemplate(), array('{ClassName}' => $name));
            $file = $this->mergePath . DIRECTORY_SEPARATOR . $name . '.php';
            if($this->confirm("Create new merge file '{$file}'?", true)) {
                file_put_contents($file, $content);
                echo "New merge created successfully.\n";
            }
        }

        /**
         * Action: History
         *
         * @access public
         * @param array<string> $args
         * @param string $from
         * @param string $to
         * @return void
         */
        public function actionHistory($args, $from, $to)
        {
            $this->createDbConnections($from, $to);
            $limit = isset($args[0]) && preg_match('/^[1-9]\\d*$/', $args[0]) ? (int) $args[0] : -1;
            if(empty($merges = $this->getMergeHistory($limit))) {
                echo "No merges have been done before.\n";
            }
            else {
                $n = count($merges);
                echo $limit > 0
                    ? Yii::t('app', "Showing the last {n} merge applied.\n|Showing the last {n} merges applied.\n", array($n))
                    : Yii::t('app', "Total of {n} merge has been previously applied.\n|Total of {n} merges have been previously applied", array($n));
                foreach($merges as $merge => $time) {
                    echo Yii::t('app', "    ({date}) {name}.\n", array('{date}' => date('Y-m-d H:i:s', $time), '{name}' => $merge));
                }
            }
        }

        /**
         * Action: New
         *
         * @access public
         * @param array<string> $args
         * @param string $from
         * @param string $to
         * @return void
         */
        public function actionNew($args, $from, $to)
        {
            $this->createDbConnections($from, $to);
            $limit = isset($args[0]) && preg_match('/^[1-9]\\d*$/', $args[0]) ? (int) $args[0] : -1;
            if(empty($merges = $this->getNewMerges())) {
                echo "No new merges found. Your system is up-to-date.\n";
            }
            else {
                $n = count($merges);
                if($limit > 0 && $n > $limit) {
                    $merges = array_slice($merges, 0, $limit);
                    echo Yii::t('app', "Showing {limit} out of {n} new merges.\n", array($n, '{limit}' => $limit));
                }
                else {
                    echo Yii::t('app', "Found {n} new merge.\n|Found {n} new merges.\n", array($n));
                }
                foreach($merges as $merge) {
                    echo Yii::t('app', "    {merge}.\n", array('{merge}' => $merge));
                }
            }
        }

    }

// -----------------------------------------------------------------------------

    interface MergeInterface
    {

        /**
         * Run Merge
         *
         * @access public
         * @return void
         */
        public function run();

        /**
         * Set: From Database
         *
         * @access public
         * @param string $environment
         * @param CDbConnection $connection
         * @return void
         */
        public function setFrom($environment, CDbConnection $connection);

    }

// -----------------------------------------------------------------------------

    abstract class BaseMerge extends CDbMigration implements MergeInterface
    {

        /**
         * @access private
         * @var array<mixed> $environment
         */
        private $from = array();

        /**
         * Set: From Database
         *
         * @final
         * @access public
         * @param string $environment
         * @param CDbConnection $connection
         * @return void
         */
        final public function setFrom($environment, CDbConnection $connection)
        {
            $this->from['environment'] = $environment;
            $this->from['connection'] = $connection;
        }

        /**
         * Get: Environment (From Database)
         *
         * @final
         * @access protected
         * @return string
         */
        final protected function getFromEnvironment()
        {
            return $this->from['environment'];
        }

        /**
         * Get: Database Connection (From Database)
         *
         * @final
         * @access protected
         * @return CDbConnection
         */
        final protected function getFromDbConnection()
        {
            return $this->from['connection'];
        }

        /**
         * Has Inserted Record?
         *
         * @final
         * @access protected
         * @param string $tableColumn
         * @param mixed $value
         * @return boolean
         */
        final protected function hasInsertedRecord($tableColumn, $value)
        {
            if(!preg_match('/^.+\\..+$/')) {
                return false;
            }
            $details = preg_split('/\\s*\\.\\s*/', $tableColumn, null, PREG_SPLIT_NO_EMPTY);
            $db = $this->getDbConnection();
            return (bool) $db->createCommand()
                ->select('COUNT(*)')
                ->from('{{' . $details[0] . '}}')
                ->where($db->quoteColumnName($details[1]) . ' = :value')
                ->bindValue(':value', $value)
                ->queryScalar();
        }

        /**
         * Get Record ID
         *
         * @final
         * @access public
         * @param string $tableColumn
         * @param mixed $value
         * @return integer
         */
        final protected function getRecordId($tableColumn, $value)
        {
            if(!preg_match('/^.+\\..+$/', $tableColumn)) {
                return false;
            }
            $details = preg_split('/\\s*\\.\\s*/', $tableColumn, null, PREG_SPLIT_NO_EMPTY);
            $db = $this->getDbConnection();
            $id = $db->createCommand()
                ->select('id')
                ->from('{{' . $details[0] . '}}')
                ->where($db->quoteColumnName($details[1]) . ' = :value')
                ->bindValue(':value', $value)
                ->queryScalar();
            return preg_match('/^[1-9]\\d*$/', $id)
                ? (int) $id
                : null;
        }

    }
