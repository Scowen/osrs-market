<?php

    namespace application\components\auth;

    use \Yii;
    use \CException;
    use \application\models\db\User;
    use \Symfony\Component\ExpressionLanguage\ExpressionLanguage as Evaluator;

    class Manager extends \CApplicationComponent
    {

        /**
         * @var     string          $connectionID
         * @access  public
         * The ID of the application component providing the database connection to a database with the tables required
         * for this component to function. Defaults to "db".
         */
        protected $connectionID = 'db';

        /**
         * @var     string          $itemTable
         * @access  public
         * The name of the database table containing authorisation items.
         */
        public $itemTable = '{{auth_items}}';

        /**
         * @var     string  $hierarchyTable
         * @access  public
         * The name of the database table containing authorisation item hierarchy.
         */
        public $hierarchyTable = '{{auth_hierarchy}}';

        /**
         * @var     string          $assignmentTable
         * @access  public
         * The name of the database table containing authorisation item assignments.
         */
        public $assignmentTable = '{{auth_assignments}}';

        /**
         * @var     \CDbConnection  $db
         * @access  protected
         * A link to the application component that is providing the database connection, as described in the
         * "connectionID" property. This is initialised automatically by default.
         */
        protected $db;

        /**
         * @var     \application\components\auth\Item[] $items
         * @access  protected
         * A cache store of authorisation item objects.
         */
        protected $items = array();

        /**
         * @var     Symfony\Component\ExpressionLanguage\ExpressionLanguage $evaluator
         * @access  private
         * Symfony's ExpressionLanguage component for evaluating business rules.
         */
        private $evaluator;


        /**
         * Check Access
         *
         * Performs an access check for the specified user.
         *
         * @access  public
         * @param   string|integer  $item               The authorisation item to check.
         * @param   integer         $user               The ID of the user being checked for access.
         * @param   array           $data               Name-value pairs to be passed as business rule parameters.
         * @return  boolean
         */
        public function checkAccess($item, $user, array $data = array())
        {
            $assignments = $this->getAuthAssignments($user);
            return $this->checkAccessRecursive($item, $user, $data, $assignments);
        }


        /**
         * Check Access (Recursive)
         *
         * @access  protected
         * @param   string|integer  $item               The item of the authorisation item.
         * @param   integer         $user               The ID of the user being checked for access.
         * @param   array           $data               Name-value pairs to be passed as business rule parameters.
         * @param   array           $assignments        The authorisation items assigned to the user being checked for access.
         * @return  boolean
         */
        protected function checkAccessRecursive($item, $user, array $data, $assignments)
        {
            if(($item = $this->getAuthItem($item)) === null) {
                return false;
            }
            Yii::trace(
                Yii::t('application', 'Checking permission "{permission}"', array('{permission}' => $item->getName())),
                'application.components.auth.Manager'
            );

            if(!isset($data['user'])) {
                $data['user'] = $user;
            }
            if($this->evaluateRule($item->getRule(), $data, $item->getData())) {
                if($item->isDefaultItem()) {
                    return true;
                }
                if(isset($assignments[$item->getId()])) {
                    $assignment = $assignments[$item->getId()];
                    if($this->evaluateRule($assignment->getRule(), $data, $assignment->getData())) {
                        return true;
                    }
                }
                $parents = $this->getDbConnection()->createCommand()
                    ->select('parent')
                    ->from($this->hierarchyTable)
                    ->where('child = :id', array(':id' => $item->getId()))
                    ->queryColumn();
                foreach($parents as $parentID) {
                    if($this->checkAccessRecursive($parentID, $user, $data, $assignments)) {
                        return true;
                    }
                }
            }
            return false;
        }


        /**
         * Add Child
         *
         * Adds an authorisation item as a child of another item.
         *
         * @access  public
         * @throws  \CException
         * @param   string|integer  $parent
         * @param   string|integer  $child
         * @return  boolean
         */
        public function addChild($parent, $child)
        {
            // Make sure that each authorisation item is valid.
            if(($parent = $this->getAuthItem($parent)) === null || ($child = $this->getAuthItem($child)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot add child hierarchy to authorisation items that do not exist.')
                );
            }
            // Make sure that the authorisation items are not the same.
            if($parent->getId() === $child->getId()) {
                throw new CException(
                    Yii::t('application', 'Cannot add "{name}" as a child of itself.', array('{name}' => $parent->getName()))
                );
            }
            // Make sure that the authorisation items' type follow the correct hierarchy.
            if($this->checkTypeInheritence($parent, $child)) {
                throw new CException(
                    Yii::t(
                        'application',
                        'Cannot add {childtype}-type as a child of {parenttype}-type.',
                        array(
                            '{parenttype}' => Item::getTypeName($parent->getType()),
                            '{childtype}' => Item::getTypeName($child->getType()),
                        )
                    )
                );
            }
            // Make sure that an infinite loop does not exist if the inheritence is added between the parent and child.
            if($this->detectLoop($parent, $child)) {
                throw new CException(
                    Yii::t(
                        'application',
                        'Cannot add "{child}" as a child of "{parent}". An infinite loop has been detected.',
                        array(
                            '{parent}' => $parent->getName(),
                            '{child}' => $child->getName(),
                        )
                    )
                );
            }
            Yii::trace(
                Yii::t(
                    'application',
                    'Adding "{child}" as a child authorisation item to "{parent}".',
                    array(
                        '{child}' => $child->getName(),
                        '{parent}' => $parent->getName(),
                    )
                ),
                'application.components.auth.Manager'
            );
            // Persist the inheritence between the parent and the child to the database.
            $this->getDbConnection()->createCommand()
                ->insert($this->hierarchyTable, array(
                    'parent' => $parent->getId(),
                    'child' => $child->getId(),
                ));
            return true;
        }


        /**
         * Remove Child
         *
         * Remove an inheritence hierarchy between two authorisation items. Note, neither authorisation items are
         * deleted.
         *
         * @access  public
         * @param   string|integer  $parent
         * @param   string|integer  $child
         * @return  boolean
         */
        public function removeChild($parent, $child)
        {
            if(($parent = $this->getAuthItem($parent)) === null || ($child = $this->getAuthItem($child)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot check child hierarchy for authorisation items that do not exist.')
                );
            }
            Yii::trace(
                Yii::t(
                    'application',
                    'Removing "{child}" as a child authorisation item to "{parent}".',
                    array(
                        '{child}' => $child->getName(),
                        '{parent}' => $parent->getName(),
                    )
                ),
                'application.components.auth.Manager'
            );
            Yii::trace(
                Yii::t(
                    'application',
                    'Removing child authorisation item "{child}" from "{parent}".',
                    array(
                        '{parent}' => $parent->getName(),
                        '{child}' => $child->getName(),
                    )
                ),
                'application.components.auth.Manager'
            );
            return $this->getDbConnection()->createCommand()->delete(
                $this->hierarchyTable,
                'parent = :parent AND child = :child',
                array('AND',
                    'parent = :parent',
                    'child  = :child',
                ),
                array(
                    ':parent' => $parent->getId(),
                    ':child' => $child->getId(),
                )
            ) > 0;
        }


        /**
         * Has Child?
         *
         * Indicates whether an authorisation item has another as a child.
         *
         * @access  public
         * @param   string|integer  $parent
         * @param   string|integer  $child
         * @return  boolean
         */
        public function hasChild($parent, $child)
        {
            if(($parent = $this->getAuthItem($parent)) === null || ($child = $this->getAuthItem($child)) === null) {
                return false;
            }
            return $this->getDbConnection()->createCommand()
                ->select('parent')
                ->from($this->hierarchyTable)
                ->where(
                    array('AND',
                        'parent = :parent',
                        'child  = :child',
                    ),
                    array(
                        ':parent' => $parent->getId(),
                        ':child' => $child->getId(),
                    )
                )
                ->queryScalar() !== false;
        }


        /**
         * Get: Children
         *
         * @access  public
         * @param   (string|integer)[]  $parents
         * @return  array
         */
        public function getChildren($parents)
        {
            $parents = (array) $parents;
            $i = 0;
            $conditions = $params = array();
            foreach($parents as $parent) {
                if(!is_scalar($parent)) {
                    continue;
                }
                ++$i;
                $conditions[] = array('OR',
                    'parents.id   = :parent' . $i,
                    'parents.name = :parent' . $i,
                );
                $params[':parent' . $i] = $parent;
            }
            if(empty($conditions)) {
                return array();
            }
            $children = $this->getDbConnection()->createCommand()
                ->select('children.id, children.name, children.type, children.description, children.rule, children.data')
                ->from($this->hierarchyTable . ' hierarchy')
                ->leftJoin($this->itemTable . ' parents', array('AND', 'hierarchy.parent = parents.id'))
                ->leftJoin($this->itemTable . ' children', array('AND', 'hierarchy.child = children.id'))
                ->where($conditions, $params)
                ->query();
            $childrenObjects = array();
            foreach($children as &$child) {
                $id = (int) $child['id'];
                $child['data'] = @unserialize($child['data']) ?: array();
                $childrenObjects[$id] = new Item(
                    $this,
                    $id,
                    $child['name'],
                    $child['type'],
                    $child['description'],
                    $child['rule'],
                    $child['data'],
                    $child['default']
                );
            }
            return $childrenObjects;
        }


        /**
         * Assign
         *
         * Assign an authorisation item to a user.
         *
         * @access  public
         * @throws  \CException
         * @param   string|integer  $item
         * @param   integer         $user
         * @param   string          $rule
         * @param   array           $data
         * @return  \application\components\auth\Assignment
         */
        public function assign($item, $user, $rule = null, array $data = array())
        {
            if(($item = $this->getAuthItem($item)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot assign an authorisation item that does not exist.')
                );
            }
            if(!preg_match('/^[1-9]\\d*$/', $user)) {
                throw new CException(
                    Yii::t(
                        'application',
                        'A valid user ID must be specified to assign the "{item}" authorisation item.',
                        array(
                            '{item}' => $item->getName(),
                        )
                    )
                );
            }
            Yii::trace(
                Yii::t(
                    'application',
                    'Assigning "{item}" to user #{user}.',
                    array(
                        '{item}' => $item->getName(),
                        '{user}' => $user,
                    )
                ),
                'application.components.auth.Manager'
            );
            $this->getDbConnection()->createCommand()->insert($this->assignmentTable, array(
                'item' => $item->getId(),
                'user' => $user,
                'rule' => $rule,
                'data' => $data,
            ));
            return new Assignment($this, $item, $user, $rule, $data);
        }


        /**
         * Revoke
         *
         * Revoke an authorisation item from a user.
         *
         * @access public
         * @param string|integer $item
         * @param integer $user
         * @return boolean
         */
        public function revoke($item, $user)
        {
            if(($item = $this->getAuthItem($item)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot revoke an authorisation item that does not exist.')
                );
            }
            if(!preg_match('/^[1-9]\\d*$/', $user)) {
                throw new CException(
                    Yii::t(
                        'application',
                        'A user ID must be specified to revoke the "{item}" authorisation item.',
                        array(
                            '{item}' => $item->getName(),
                        )
                    )
                );
            }
            return $this->getDbConnection()->createCommand()->delete(
                $this->assignmentTable,
                array('AND',
                    'item = :item',
                    'user = :user',
                ),
                array(
                    ':item' => $item->getId(),
                    ':user' => $user,
                )
            ) > 0;
        }


        /**
         * Is Assigned?
         *
         * Has an authorisation item been assigned to the specified user?
         *
         * @access  public
         * @param   string|integer  $item
         * @param   integer         $user
         * @return  boolean
         */
        public function isAssigned($item, $user)
        {
            if(($item = $this->getAuthItem($item)) === null) {
                return false;
            }
            return $this->getDbConnection()->createCommand()
                ->select('item')
                ->from($this->assignmentTable)
                ->where(
                    array('AND',
                        'item = :item',
                        'user = :user',
                    ),
                    array(
                        ':item' => $item->getId(),
                        ':user' => $user,
                    )
                )
                ->queryScalar() !== false;
        }


        /**
         * Get: Auth Assignment
         *
         * @access  public
         * @throws  \CException
         * @param   string|integer  $item
         * @param   integer         $user
         * @return  \application\components\auth\Assignment
         */
        public function getAuthAssignment($item, $user)
        {
            if(($item = $this->getAuthItem($item)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot retrieve assignment information for an authorisation item that does not exist.')
                );
            }
            if(!preg_match('/^[1-9]\\d*$/', $user)) {
                throw new CException(
                    Yii::t(
                        'application',
                        'A user ID must be specified to retrieve assignment information for the authorisation item "{item}".',
                        array(
                            '{item}' => $item->getName(),
                        )
                    )
                );
            }
            $assignment = $this->getDbConnection()->createCommand()
                ->select()
                ->from($this->assignmentTable)
                ->where(
                    array('AND',
                        'item = :item',
                        'user = :user',
                    ),
                    array(
                        ':item' => $item->getId(),
                        ':user' => $user,
                    )
                )
                ->queryRow();
            if($assignment !== false) {
                $assignment['data'] = @unserialize($assignment['data']) ?: array();
                return new Assignment($this, $item, $user, $assignment['rule'], $assignment['data']);
            }
        }


        /**
         * Get: Auth Assignments
         *
         * Returns all the assignments for the specified user.
         *
         * @access public
         * @param integer $user
         * @return \application\components\auth\Assignment[]
         */
        public function getAuthAssignments($user)
        {
            // If the user specified is null, then there isn't any user currently logged-in, therefore there won't be
            // any authorisation items assigned.
            if($user === null) {
                return array();
            }
            if(!preg_match('/^[1-9]\\d*$/', $user)) {
                var_dump($user);exit;
                throw new CException(
                    Yii::t('application', 'A user ID must be specified to retrieve assignment information.')
                );
            }
            $rows = $this->getDbConnection()->createCommand()
                ->select()
                ->from($this->assignmentTable)
                ->where(
                    array('AND',
                        'user = :user'
                    ),
                    array(
                        ':user' => $user,
                    )
                )
                ->query();
            $assignments = array();
            foreach($rows as $assignment) {
                $item = $this->getAuthItem($assignment['item']);
                if($item === null) {
                    continue;
                }
                $assignment['data'] = @unserialize($assignment['data']) ?: array();
                $assignments[$item->getId()] = new Assignment($this, $item, $user, $assignment['rule'], $assignment['data']);
            }
            return $assignments;
        }


        /**
         * Save Auth Assignment
         *
         * Save changes made to the business rule and related data of an authorisation assignment.
         *
         * @access public
         * @param \application\components\auth\Assignment $assignment
         * @return boolean
         */
        public function saveAuthAssignment(Assignment $assignment)
        {
            return $this->getDbConnection()->createCommand()->update(
                $this->assignmentTable,
                array(
                    'rule' => $assignment->getRule(),
                    'data' => serialize($assignment->getData()),
                ),
                array('AND',
                    'item = :item',
                    'user = :user',
                ),
                array(
                    ':item' => $assignment->getItem()->getId(),
                    ':user' => $assignment->getUser(),
                )
            ) > 0;
        }


        /**
         * Get: Auth Items
         *
         * Returns the authorisation items of certain types and users.
         *
         * @access public
         * @param integer $type
         * @param integer $user
         * @return \application\components\auth\Item[]
         */
        public function getAuthItems($type = null, $user = null)
        {
            $command = $this->getDbConnection()->createCommand()
                ->select('items.*')
                ->from($this->itemTable . ' items');
            if($user !== null) {
                $command->leftJoin($this->assignmentTable . ' assignments', 'assignments.item = items.id');
                if(!$type !== null) {
                    $command->where(both);
                }
                else {
                    $command->where(justuser);
                }
            }
            elseif($type !== null) {
                $command->where(justtype);
            }
            $items = array();
            $rows = $command->query();
            foreach($rows as $item) {
                $id = (int) $items['id'];
                if(!isset($this->items[(int) $id])) {
                    $item['data'] = @unserialize($item['data']) ?: array();
                    $this->items[(int) $id] = new Item(
                        $this,
                        $id,
                        $item['name'],
                        $item['type'],
                        $item['description'],
                        $item['rule'],
                        $item['data'],
                        $item['default']
                );
                }
                $items[$id] = $this->items[(int) $id];
            }
            return $items;
        }


        /**
         * Create Auth Item
         *
         * Create an authorisation item, which represents an action permissions (for example, creating a blog post). It
         * has three different types (role, operation, and task) which may be used to create a hierarchy. Higher-level
         * items inherit permissions represented by lower-level items.
         *
         * @access  public
         * @throws  \CException
         * @param   string          $name           The authorisation name. This must be a unique identifier.
         * @param   integer         $type           The item type (role, operation, or task). Correct values are stored
         *                                          as constants in the authorisation object class.
         * @param   string          $description    The description of the authorisation item.
         * @param   string          $rule           The business rule to be evaluated on an access check for this
         *                                          authorisation item.
         * @param   array           $data           Any data that is associated with the business rule of this
         *                                          authorisation item.
         * @param   boolean         $default        Whether the authorisation should be assigned to users by default.
         * @return  \application\components\auth\Item
         */
        public function createAuthItem($name, $type, $description = null, $rule = null, array $data = array(), $default = false)
        {
            if(!is_string($name) || !preg_match('', $name)) {
                throw new CException(
                    Yii::t('application', 'Cannot create a new authorisation item; the name must be a valid PHP label.')
                );
            }
            if($this->getAuthItem($name) !== null) {
                throw new CException(
                    Yii::t(
                        'application',
                        'An authorisation item with the name "{item}" already exists in the database.',
                        array(
                            '{item}' => $name,
                        )
                    )
                );
            }
            $inserted = $this->getDbConnection()->createCommand()->insert($this->itemTable, array(
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'rule' => $rule,
                'data' => serialize($data),
            )) > 0;
            if(!$inserted) {
                throw new CException(
                    Yii::t(
                        'application',
                        'Cannot persist the authorisation item "{item}" to the database.',
                        array(
                            '{item}' => $name,
                        )
                    )
                );
            }
            $id = $this->getDbConnection()->getLastInsertID();
            $this->items[(int) $id] = new Item(
                $this,
                $id,
                $name,
                $type,
                $description,
                $rule,
                $data,
                $default
            );
            return $this->items[(int) $id];
        }


        /**
         * Remove Auth Item
         *
         * @access  public
         * @param   string|integer  $item
         * @return  boolean
         */
        public function removeAuthItem($item)
        {
            if(($item = $this->getAuthItem($item)) === null) {
                return true;
            }
            $this->getDbConnection()->createCommand()->delete(
                $this->assignmentTable,
                array('OR',
                    'parent = :item',
                    'child  = :item',
                ),
                array(
                    ':item' => $item->getId(),
                )
            );
            $deleted = $this->getDbConnection()->createCommand()->delete(
                $this->itemTable,
                array('AND',
                    'id = :item',
                ),
                array(
                    ':item' => $item->getId(),
                )
            ) > 0;
            if($deleted) {
                unset($this->items[(int) $item->getId()], $item);
                return true;
            }
            return false;
        }


        /**
         * Get: Auth Item
         *
         * Fetch an authorisation item from the database (or cache if it has already been loaded) with the specific name
         * or ID.
         *
         * @access public
         * @param string|integer $item
         * @return \application\components\auth\Item
         */
        public function getAuthItem($item)
        {
            if($item instanceof Item) {
                return $item;
            }
            if(preg_match('/^[1-9]\\d*$/', $item) && isset($this->items[(int) $item])) {
                return $this->items[(int) $item];
            }
            $item = $this->getDbConnection()->createCommand()
                ->select()
                ->from($this->itemTable . ' items')
                ->where(
                    array('OR',
                        'id     = :item',
                        'name   = :item',
                    ),
                    array(
                        ':item' => $item,
                    )
                )
                ->queryRow();
            if($item) {
                $id = (int) $item['id'];
                $item['data'] = @unserialize($item['data']) ?: array();
                $this->items[$id] = new Item(
                    $this,
                    $id,
                    $item['name'],
                    $item['type'],
                    $item['description'],
                    $item['rule'],
                    $item['data'],
                    $item['default']
                );
                return $this->items[$id];
            }
        }


        /**
         * Save Auth Item
         *
         * Save changes to an authorisation item to persistent storage (database).
         *
         * @access public
         * @param \application\components\auth\Item
         * @return boolean
         */
        public function saveAuthItem(Item $item)
        {
            return $item->save();
        }


        /**
         * Save
         *
         * Save the authorisation data to persistent storage (database).
         *
         * @access public
         * @return void
         */
        public function save()
        {
        }


        /**
         * Clear All
         *
         * Remove all authorisation data from persistent storage (database).
         *
         * @access public
         * @return void
         */
        public function clearAll()
        {
            $this->clearAuthAssignments();
            $this->getDbConnection()->createCommand()->delete($this->hierarchyTable);
            $this->getDbConnection()->createCommand()->delete($this->itemTable);
        }


        /**
         * Clear Auth Assignments
         *
         * Remove all authorisation items from being assigned to users. Note, this does not delete the authorisation items themselves.
         *
         * @access public
         * @return void
         */
        public function clearAuthAssignments()
        {
            $this->getDbConnection()->createCommand()->delete($this->assignmentTable);
        }


        /**
         * Detect Loop
         *
         * @access  protected
         * @throws  \CException
         * @param   string|integer  $parent
         * @param   string|integer  $child
         * @return  boolean
         */
        public function detectLoop($parent, $child)
        {
            if(($parent = $this->getAuthItem($parent)) === null || ($child = $this->getAuthItem($child)) === null) {
                throw new CException(
                    Yii::t('application', 'Cannot check for infinite inheritence loop for authorisation items that do not exist.')
                );
            }
            if($parent->getId() == $child->getId()) {
                return true;
            }
            foreach($this->getItemChildren($child) as $grandchild) {
                if($this->detectLoop($child, $grandchild)) {
                    return true;
                }
            }
            return false;
        }


        /**
         * Get: Database Connection
         *
         * @access  protected
         * @throws  \CException
         * @return  \CDbConnection
         */
        public function getDbConnection()
        {
            if($this->db !== null || ($this->db = Yii::app()->getComponent($this->connectionID)) instanceof \CDbConnection) {
                return $this->db;
            }
            else {
                throw new CException(
                    Yii::t('application', 'The connection ID for the Authorisation Manager is invalid. Please make sure it refers to the ID of a CDbConnection application component.')
                );
            }
        }


        /**
         * Create Role
         *
         * Creates a role authorisation item.
         *
         * @access  public
         * @param   string          $name
         * @param   string          $description
         * @param   string          $rule
         * @param   array           $data
         * @return  \application\components\auth\Item
         */
        public function createRole($name, $description = null, $rule = null, array $data = array())
        {
            return $this->createAuthItem($name, Item::TYPE_ROLE, $description, $rule, $data);
        }


        /**
         * Create Task
         *
         * Create a task authorisation item.
         *
         * @access  public
         * @param   string          $name
         * @param   string          $description
         * @param   string          $rule
         * @param   array           $data
         * @return  \application\components\auth\Item
         */
        public function createTask($name, $description = null, $rule = null, array $data = array())
        {
            return $this->createAuthItem($name, Item::TYPE_TASK, $description, $rule, $data);
        }


        /**
         * Create Operation
         *
         * Create an operation authorisation item.
         *
         * @access  public
         * @param   string          $name
         * @param   string          $description
         * @param   string          $rule
         * @param   array           $data
         * @return  \application\components\auth\Item
         */
        public function createOperation($name, $description = null, $rule = null, array $data = array())
        {
            return $this->createAuthItem($name, Item::TYPE_OPERATION, $description, $rule, $data);
        }


        /**
         * Get: Roles
         *
         * Fetch all authorisation items of type "role", optionally belonging to a specific user ID.
         *
         * @access  public
         * @param   integer         $user
         * @return  \application\components\auth\Item[]
         */
        public function getRoles($user = null)
        {
            return $this->getAuthItems(Item::TYPE_ROLE, $user);
        }


        /**
         * Get: Tasks
         *
         * Fetch all authorisation items of type "task", optionally belonging to a specific user ID.
         *
         * @access  public
         * @param   integer         $user
         * @return  \application\components\auth\Item[]
         */
        public function getTasks($user = null)
        {
            return $this->getAuthItems(Item::TYPE_TASK, $user);
        }


        /**
         * Get: Operations
         *
         * Fetch all authorisation items of type "operation", optionally belonging to a specific user ID.
         *
         * @access  public
         * @param   integer         $user
         * @return  \application\components\auth\Item[]
         */
        public function getOperations($user = null)
        {
            return $this->getAuthItems(Item::TYPE_OPERATION, $user);
        }


        /**
         * Evaluate Business Rule
         *
         * @access  public
         * @param   string          $rule
         * @param   array           $parameters
         * @param   array           $data
         * @return  boolean
         */
        public function evaluateRule($rule, array $parameters = array(), array $data = array())
        {
            if(empty($rule)) {
                return true;
            }
            if($this->evaluator === null) {
                $this->evaluator = new Evaluator;
            }
            $parameters = \CMap::mergeArray($data, $parameters);
            return $this->evaluator->evaluate($rule, $parameters);
        }


        /**
         * Check Type Inheritence
         *
         * Check that an authorisation item's type can legally be inherited by another.
         *
         * @access protected
         * @param \application\components\auth\Item $parent
         * @param \application\components\auth\Item $child
         * @return boolean
         */
        public function checkTypeInheritence(Item $parent, Item $child)
        {
            return $parent->getType() > $child->getType();
        }

    }
