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
use User\Model\UserEventTaskEntity;

class UserEventTaskMapper
{
    protected $tableName = 'user_event_task';
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

        if (isset($filter['event_task_id'])) {
            $where->equalTo("event_task_id", $filter['event_task_id']);
        }

        if (isset($filter['user_id'])) {
            $where->equalTo("user_id", $filter['user_id']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

        if ($paginated) {
            $entityPrototype = new UserEventTaskEntity();
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

            $entityPrototype = new UserEventTaskEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(UserEventTaskEntity $userEventTask)
    {
        $hydrator = new ClassMethods();
        $data = $hydrator->extract($userEventTask);

        if ($userEventTask->getId()) {
            // update action
            $action = $this->sql->update();
            $action->set($data);
            $action->where(['id' => $userEventTask->getId()]);
        } else {
            // insert action
            $action = $this->sql->insert();
            unset($data['id']);
            $action->values($data);
        }
        $statement = $this->sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();

        if (!$userEventTask->getId()) {
            $userEventTask->setId($result->getGeneratedValue());
        }
        return $result;
    }

    public function getUserEventTask($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $userEventTask = new UserEventTaskEntity();
        $hydrator->hydrate($result, $userEventTask);

        return $userEventTask;
    }

    public function getUserEventTaskByUserIdAndEventId($user_id, $event_id)
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
        $userEventTask = new UserEventTaskEntity();
        $hydrator->hydrate($result, $userEventTask);

        return $userEventTask;
    }

    public function delete($id)
    {
        $delete = $this->sql->delete();
        $delete->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    public function getUserEventTasks($paginated=false, $filter = [], $order = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'id',
            'event_id',
            'user_id',
            'is_paid',
            'created_datetime',
        ]);
        $select->join(
            'event',
            $this->tableName . ".event_id = event.id",
            [
                'name',
                'venue',
                'event_date',
                'price',
            ],
            $select::JOIN_INNER
        );

        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['user_id'])) {
            $where->equalTo($this->tableName . ".user_id", $filter['user_id']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        // echo $select->getSqlString($this->dbAdapter->getPlatform()) . "<br>";

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
