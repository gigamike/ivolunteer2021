<?php
namespace User\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use User\Model\UserEntity;

class UserMapper
{
    protected $tableName = 'user';
    protected $dbAdapter;
    protected $sql;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
        $this->sql->setTable($this->tableName);
    }

    public function fetch($paginated=false, $filter = [], $order=[])
    {
        $select = $this->sql->select();
        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['id'])) {
            $where->equalTo("id", $filter['id']);
        }

        if (isset($filter['id_not'])) {
            $where->notEqualTo("id", $filter['id_not']);
        }

        if (isset($filter['active'])) {
            $where->equalTo("active", $filter['active']);
        }

        if (isset($filter['role'])) {
            $where->equalTo("role", $filter['role']);
        }

        if (isset($filter['referral_id'])) {
            $where->equalTo("referral_id", $filter['referral_id']);
        }

        if (isset($filter['first_name_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like("first_name", "%" . $filter['first_name_keyword'] . "%")
            );
        }

        if (isset($filter['last_name_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like("last_name", "%" . $filter['last_name_keyword'] . "%")
            );
        }

        if (isset($filter['email_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like("email", "%" . $filter['email_keyword'] . "%")
            );
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

        if ($paginated) {
            $entityPrototype = new UserEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);

            $paginatorAdapter = new DbSelect(
                $select,
                $this->dbAdapter,
                $resultset
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();

            $entityPrototype = new UserEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(UserEntity $user)
    {
        $hydrator = new ClassMethods();
        $data = $hydrator->extract($user);

        if ($user->getId()) {
            // update action
            $action = $this->sql->update();
            $action->set($data);
            $action->where(['id' => $user->getId()]);
        } else {
            // insert action
            $action = $this->sql->insert();
            unset($data['id']);
            $action->values($data);
        }
        $statement = $this->sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();

        if (!$user->getId()) {
            $user->setId($result->getGeneratedValue());
        }
        return $result;
    }

    public function getUser($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $user = new UserEntity();
        $hydrator->hydrate($result, $user);

        return $user;
    }

    public function getUserByEmail($email)
    {
        $select = $this->sql->select();
        $select->where(['email' => $email]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $user = new UserEntity();
        $hydrator->hydrate($result, $user);

        return $user;
    }

    public function dynamicSalt()
    {
        $dynamicSalt = '';
        for ($i = 0; $i < 50; $i++) {
            $dynamicSalt .= chr(rand(33, 126));
        }

        return $dynamicSalt;
    }

    public function randomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function delete($id)
    {
        $delete = $this->sql->delete();
        $delete->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    public function getTopVolunteers($paginated=false, $filter = [], $order = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'id',
            'first_name',
            'last_name',
            'email',
            'points' => new \Zend\Db\Sql\Expression("(
                SELECT SUM(event_task.points) 
                FROM event_task
                INNER JOIN user_event_task ON user_event_task.event_task_id = event_task.id
                WHERE user_event_task.user_id = user.id)"),
            'last_attended' => new \Zend\Db\Sql\Expression("(
                SELECT MAX(user_event.attend_datetime) 
                FROM user_event
                WHERE user_event.user_id = user.id)"),
        ]);
        
        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['user_id'])) {
            $where->equalTo($this->tableName . ".user_id", $filter['user_id']);
        }

        if (isset($filter['role'])) {
            $where->equalTo("user.role", $filter['role']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform()) . "<br>"; exit();

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                $select,
                $this->dbAdapter
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet;
                $resultSet->initialize($result);
            }
        }

        return $resultSet;
    }
}
