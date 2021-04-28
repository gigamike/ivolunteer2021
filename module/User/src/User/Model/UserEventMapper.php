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

class UserEventMapper
{
    protected $tableName = 'user_event';
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

        if (isset($filter['user_id'])) {
            $where->equalTo("user_id", $filter['user_id']);
        }

        if (isset($filter['event_id'])) {
            $where->equalTo("event_id", $filter['event_id']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform()); exit();

        if ($paginated) {
            $entityPrototype = new UserEventEntity();
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

            $entityPrototype = new UserEventEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(UserEventEntity $userEvent)
    {
        $hydrator = new ClassMethods();
        $data = $hydrator->extract($userEvent);

        if ($userEvent->getId()) {
            // update action
            $action = $this->sql->update();
            $action->set($data);
            $action->where(['id' => $userEvent->getId()]);
        } else {
            // insert action
            $action = $this->sql->insert();
            unset($data['id']);
            $action->values($data);
        }
        $statement = $this->sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();

        if (!$userEvent->getId()) {
            $userEvent->setId($result->getGeneratedValue());
        }
        return $result;
    }

    public function getUserEvent($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $userEvent = new UserEventEntity();
        $hydrator->hydrate($result, $userEvent);

        return $userEvent;
    }

    public function getUserEventByUserIdAndEventId($user_id, $event_id)
    {
        $select = $this->sql->select();
        $select->where(['user_id' => $user_id]);
        $select->where(['event_id' => $event_id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $userEvent = new UserEventEntity();
        $hydrator->hydrate($result, $userEvent);

        return $userEvent;
    }

    public function delete($id)
    {
        $delete = $this->sql->delete();
        $delete->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    public function getCount($filter = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'count_id' => new \Zend\Db\Sql\Expression("COUNT(" . $this->tableName . ".id)"),
        ]);

        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['user_id'])) {
            $where->equalTo("user_id", $filter['user_id']);
        }

        if (isset($filter['event_id'])) {
            $where->equalTo("event_id", $filter['event_id']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform()) . "<br />";

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        return $result;
    }

    public function getUserEvents($paginated=false, $filter = [], $order = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'id',
            'event_id',
            'user_id',
            'attend_datetime',
            'created_datetime',
            'points' => new \Zend\Db\Sql\Expression("(
                SELECT SUM(event_task.points) 
                FROM event_task
                INNER JOIN user_event_task ON user_event_task.event_task_id = event_task.id
                WHERE event_task.event_id = user_event.event_id)"),
        ]);
        $select->join(
            'event',
            $this->tableName . ".event_id = event.id",
            [
                'name',
                'venue',
                'event_date',
            ],
            $select::JOIN_INNER
        );
        $select->join(
            'user',
            $this->tableName . ".user_id = user.id",
            [
                'first_name',
                'last_name',
                'email',
            ],
            $select::JOIN_INNER
        );
        
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

        // echo $select->getSqlString($this->dbAdapter->getPlatform()) . "<br>";exit();

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
