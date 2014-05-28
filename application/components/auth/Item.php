<?php

    namespace application\components\auth;

    use \Yii;
    use \CException;

    class Item extends \CComponent
    {

        const TYPE_OPERATION    = 0;
        const TYPE_TASK         = 1;
        const TYPE_ROLE         = 2;
        const TYPE_GROUP        = 3;

        protected $manager;
        protected $id;
        protected $name;
        protected $type;
        protected $description;
        protected $rule;
        protected $data;
        protected $default;


        /**
         * Constructor
         *
         * @access  public
         * @throws  \CException
         * @param   \application\components\auth\Manager $manager
         * @param   integer         $id
         * @param   string          $name
         * @param   integer         $type
         * @param   string          $description
         * @param   string          $rule
         * @param   array           $data
         * @param   boolean         $default
         * @return  void
         */
        public function __construct(Manager $manager, $id, $name, $type, $description = null, $rule = null, array $data = array(), $default = false)
        {
            if(!preg_match('/^[1-9]\\d*$/', $id)) {
                throw new CException(
                    Yii::t('application', 'A valid authorisation item ID (positive integer) must be supplied.')
                );
            }
            if(!preg_match(VALIDLABEL, trim($name))) {
                throw new CException(
                    Yii::t('application', 'A valid PHP label (allowing spaces) must be supplied for the authorisation name.')
                );
            }
            if(!$this->checkValidType($type)) {
                throw new CException(
                    Yii::t('application', 'A valid authorisation item type, as specified in the Item object class, must be supplied')
                );
            }
            $this->manager      = $manager;
            $this->id           = (int) $id;
            $this->name         = trim($name);
            $this->type         = (int) $type;
            $this->description  = $description  ?: null;
            $this->rule         = $rule         ?: null;
            $this->data         = $data;
            $this->default      = (bool) $default;
        }


        /**
         * Check Valid Type
         *
         * Checks whether the value passed for the item type is valid (whether it exists as a constant within this class
         * definition).
         *
         * @access  protected
         * @param   integer         $type
         * @return  boolean
         */
        protected function checkValidType($type)
        {
            static $types;
            if($types === null) {
                $types = (new \ReflectionClass($this))->getConstants();
                $keys = array_filter(array_keys($types), function($key) {
                    return strtoupper(substr($key, 0, 5)) == 'TYPE_';
                });
                $types = array_intersect_key($types, array_flip($keys));
            }
            return preg_match('/^(0|[1-9]\\d*)$/', $type) && in_array((int) $type, $types);
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
         * Get: Item ID
         *
         * @access  public
         * @return  integer
         */
        public function getId()
        {
            return $this->id;
        }


        /**
         * Get: Item Name
         *
         * @access public
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }


        /**
         * Set: Item Name
         *
         * @chainable
         * @access  public
         * @throws  \CException
         * @param   string          $name
         * @return  self
         */
        public function setName($name)
        {
            if(!is_string($name) || empty($name) || strlen($name) > 64) {
                throw new CException(
                    Yii::t('application', 'A non-empty string no longer than 64 characters must be supplied for the authorisation item name.')
                );
            }
            $this->name = $name;
            return $this;
        }


        /**
         * Get: Item Type
         *
         * @access  public
         * @return  integer
         */
        public function getType()
        {
            return $this->type;
        }


        /**
         * Set: Item Type
         *
         * @chainable
         * @access  public
         * @throws  \CException
         * @param   integer         $type
         * @param   boolean         $force
         * @return  self
         */
        public function setType($type, $force = false)
        {
            throw new CException('NOT YET IMPLEMENTED.');
            // Use $this->checkValidType.
        }


        /**
         * Get: Item Description
         *
         * @access  public
         * @return  string
         */
        public function getDescription()
        {
            return $this->description;
        }


        /**
         * Set: Item Description
         *
         * @chainable
         * @access  public
         * @throws  \CException
         * @param   string          $description
         * @return  self
         */
        public function setDescription($description)
        {
            if($description !== null || !is_string($description)) {
                throw new CException(
                    Yii::t('application', 'A string must be supplied for the authorisation item description.')
                );
            }
            $this->description = $description ?: null;
            return $this;
        }


        /**
         * Get: Item Rule
         *
         * @access  public
         * @return  string
         */
        public function getRule()
        {
            return $this->rule;
        }


        /**
         * Set: Item Rule
         *
         * @chainable
         * @access  public
         * @throws  \CException
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
         * Get: Item Data
         *
         * @access  public
         * @return  array
         */
        public function getData()
        {
            return $this->data;
        }


        /**
         * Set: Item Data
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
         * Is Default Item?
         *
         * @access  public
         * @return  boolean
         */
        public function isDefaultItem()
        {
            return $this->default;
        }


        /**
         * Set: Default?
         *
         * @chainable
         * @access public
         * @param boolean $default
         * @return self
         */
        public function setDefault($default)
        {
            $this->default = (bool) $default;
            return $this;
        }


        /**
         * Save Item
         *
         * Save changes made to the current item to persistent storage (database).
         *
         * @access  public
         * @return  boolean
         */
        public function save()
        {
            return $this->getManager()->getDbConnection()->createCommand()->update(
                $this->getManager()->itemTable,
                array(
                    'name'          => $this->getName(),
                    'type'          => $this->getType(),
                    'description'   => $this->getDescription(),
                    'rule'          => $this->getRule(),
                    'data'          => serialize($this->getData()),
                    'default'       => $this->isDefaultItem(),
                ),
                array('AND',
                    'id = :id',
                ),
                array(
                    ':id'           > $this->getId(),
                )
            ) > 0;
        }


        /**
         * Add Child
         *
         * @access  public
         * @param   string|integer  $item
         * @return  boolean
         */
        public function addChild($item)
        {
            return $this->getManager()->addChild($this, $item);
        }


        /**
         * Remove Child
         *
         * @access  public
         * @param   string|integer  $item
         * @return  boolean
         */
        public function removeChild($item)
        {
            return $this->getManager()->removeChild($this, $item);
        }


        /**
         * Has Child?
         *
         * @access  public
         * @param   string|integer  $item
         * @return  boolean
         */
        public function hasChild($item)
        {
            return $this->getManager()->hasChild($this, $item);
        }


        /**
         * Get: Children
         *
         * @access  public
         * @return  \application\components\auth\Item[]
         */
        public function getChildren()
        {
            return $this->getManager()->getChildren($this);
        }


        /**
         * Assign
         *
         * Assign an authorisation item to a user.
         *
         * @access  public
         * @param   integer         $user
         * @param   string          $rule
         * @param   array           $data
         * @return  \application\components\auth\Assignment
         */
        public function assign($user, $rule = null, array $data = array())
        {
            return $this->getManager()->assign($this, $user, $rule, $data);
        }


        /**
         * Revoke
         *
         * Revoke an authorisation item from a user.
         *
         * @access  public
         * @param   integer         $user
         * @return  boolean
         */
        public function revoke($user)
        {
            return $this->getManager()->revoke($this, $user);
        }


        /**
         * Is Assigned?
         *
         * Has this authorisation item been assigned to the specified user?
         *
         * @access  public
         * @param   integer         $user
         * @return  boolean
         */
        public function isAssigned($user)
        {
            return $this->getManager()->isAssigned($this, $user);
        }


        /**
         * Get: Assignment
         *
         * Returns an assignment for this authorisation item with a specific user.
         *
         * @access  public
         * @param   integer         $user
         * @return  \application\components\auth\Assignment
         */
        public function getAssignment($user)
        {
            return $this->getManager()->getAssignment($this, $user);
        }

    }
