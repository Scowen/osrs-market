<?php

    namespace application\components\auth;

    use \Yii;
    use \CException;

    class Assignment extends \CComponent
    {

        /**
         * @var     \application\components\auth\Manager    $manager
         * @access  protected
         */
        protected $manager;

        /**
         * @var     \application\components\auth\Item       $item
         * @access  protected
         */
        protected $item;

        /**
         * @var     integer         $user
         * @access  protected
         */
        protected $user;

        /**
         * @var     string          $rule
         * @access  protected
         */
        protected $rule;

        /**
         * @var     array           $data
         * @access  protected
         */
        protected $data;


        /**
         * Constructor
         *
         * @access  public
         * @param   \application\components\auth\Manager $manager
         * @param   string|integer  $item
         * @param   integer         $user
         * @param   string          $rule
         * @param   array           $data
         * @return  void
         */
        public function __construct(Manager $manager, $item, $user, $rule = null, array $data = array())
        {
            $this->manager = $manager;
            if(($item = $this->getManager()->getAuthItem($item)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot create an authorisation assignment object for an item that does not exist.')
                );
            }
            if(!preg_match('/^[1-9]\\d*$/', $user)) {
                throw new CException(
                    Yii::t('application', 'A valid user ID must be supplied to create an authorisation assignment object.')
                );
            }
            $this->item = $item;
            $this->user = (int) $user;
            $this->rule = is_string($rule) && !empty($rule)
                ? $rule
                : null;
            $this->data = $data;
        }


        /**
         * Get: Authorisation Manager
         *
         * @access public
         * @return \application\components\auth\Manager
         */
        public function getManager()
        {
            return $this->manager;
        }


        /**
         * Get: User ID
         *
         * @access  public
         * @return  integer
         */
        public function getUser()
        {
            return $this->user;
        }


        /**
         * Get: Item
         *
         * @access  public
         * @return  \application\components\auth\Item
         */
        public function getItem()
        {
            return $this->item;
        }


        /**
         * Get: Business Rule
         *
         * @access public
         * @return string
         */
        public function getRule()
        {
            return $this->rule;
        }


        /**
         * Set: Business Rule
         *
         * @chainable
         * @access  public
         * @param   string          $rule
         * @return  self
         */
        public function setRule($rule)
        {
            if($rule !== null || !is_string($rule)) {
                throw new CException(
                    Yii::t('application', 'A string must be supplied for the authorisation item\' business rule.')
                );
            }
            $this->rule = $rule ?: null;
            return $this;
        }


        /**
         * Get: Data
         *
         * @access  public
         * @return  array
         */
        public function getData()
        {
            return $this->data;
        }


        /**
         * Set: Data
         *
         * @chainable
         * @access  public
         * @param   array           $data
         * @return  self
         */
        public function setData(array $data)
        {
            $this->data = $data;
            return $this;
        }


        /**
         * Save Assignment
         *
         * @access public
         * @return boolean
         */
        public function save()
        {
            return $this->getManager()->getDbConnection()->createCommand()->update(
                $this->getManager()->assignmentTable,
                array(
                    'rule' => $this->getRule(),
                    'data' => serialize($this->getData()),
                ),
                array('AND',
                    'user = :user',
                    'item = :item',
                ),
                array(
                    ':user' => $this->getUser(),
                    ':item' => $this->getItem()->getId(),
                )
            ) > 0;
        }

    }
