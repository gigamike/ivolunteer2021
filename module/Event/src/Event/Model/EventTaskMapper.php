<?php
namespace Event\Model;

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
use Event\Model\EventTaskEntity;

class EventTaskMapper
{
    protected $tableName = 'event_task';
    protected $dbAdapter;
    protected $sql;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
        $this->sql->setTable($this->tableName);
    }

    public function fetch($paginated=false, $filter = [], $order=[], $limit = null)
    {
        $select = $this->sql->select();
        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['id'])) {
            $where->equalTo("id", $filter['id']);
        }

        if (isset($filter['event_id'])) {
            $where->equalTo("event_id", $filter['event_id']);
        }

        if (isset($filter['id_not'])) {
            $where->notEqualTo("id", $filter['id_not']);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        if (count($order) > 0) {
            $select->order($order);
        }

        if ($limit) {
            $select->limit($limit);
        }

        //echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

        if ($paginated) {
            $entityPrototype = new EventTaskEntity();
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

            $entityPrototype = new EventTaskEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(EventTaskEntity $event)
    {
        $hydrator = new ClassMethods();
        $data = $hydrator->extract($event);

        if ($event->getId()) {
            // update action
            $action = $this->sql->update();
            $action->set($data);
            $action->where(['id' => $event->getId()]);
        } else {
            // insert action
            $action = $this->sql->insert();
            unset($data['id']);
            $action->values($data);
        }
        $statement = $this->sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();

        if (!$event->getId()) {
            $event->setId($result->getGeneratedValue());
        }
        return $result;
    }

    public function getEventTask($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $event = new EventTaskEntity();
        $hydrator->hydrate($result, $event);

        return $event;
    }

    public function delete($id)
    {
        $delete = $this->sql->delete();
        $delete->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }
}
