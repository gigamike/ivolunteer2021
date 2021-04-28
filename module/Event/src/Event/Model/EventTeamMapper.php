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
use Event\Model\EventTeamEntity;

class EventTeamMapper
{
    protected $tableName = 'event_team';
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

        if (isset($filter['name'])) {
            $where->equalTo("name", $filter['name']);
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
            $entityPrototype = new EventTeamEntity();
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

            $entityPrototype = new EventTeamEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(EventTeamEntity $event)
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

    public function getEventTeam($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $event = new EventTeamEntity();
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

    public function getSumTeamLimit($filter = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'sum_team_limit' => new \Zend\Db\Sql\Expression("SUM(" . $this->tableName . ".team_limit)"),
        ]);

        $where = new \Zend\Db\Sql\Where();

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

    public function getTeamStandings($paginated=false, $filter = [], $order = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'id',
            'name',
            'points' => new \Zend\Db\Sql\Expression("(
                SELECT SUM(event_task.points) 
                FROM event_task
                INNER JOIN user_event_task ON user_event_task.event_task_id = event_task.id
                INNER JOIN event_team_member ON event_team_member.user_id = user_event_task.user_id
                INNER JOIN event ON event.id = event_task.event_id
                WHERE event.event_type = 'team'
                    AND event_team_member.event_team_id = event_team.id)"),
        ]);
        
        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['event_id'])) {
            $where->equalTo($this->tableName . ".event_id", $filter['event_id']);
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
