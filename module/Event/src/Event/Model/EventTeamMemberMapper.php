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
use Event\Model\EventTeamMemberEntity;

class EventTeamMemberMapper
{
    protected $tableName = 'event_team_member';
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

        if (isset($filter['event_team_id'])) {
            $where->equalTo("event_team_id", $filter['event_team_id']);
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

        if ($limit) {
            $select->limit($limit);
        }

        //echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

        if ($paginated) {
            $entityPrototype = new EventTeamMemberEntity();
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

            $entityPrototype = new EventTeamMemberEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(EventTeamMemberEntity $eventTeamMember)
    {
        $hydrator = new ClassMethods();
        $data = $hydrator->extract($eventTeamMember);

        if ($eventTeamMember->getId()) {
            // update action
            $action = $this->sql->update();
            $action->set($data);
            $action->where(['id' => $eventTeamMember->getId()]);
        } else {
            // insert action
            $action = $this->sql->insert();
            unset($data['id']);
            $action->values($data);
        }
        $statement = $this->sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();

        if (!$eventTeamMember->getId()) {
            $eventTeamMember->setId($result->getGeneratedValue());
        }
        return $result;
    }

    public function getEventTeamMember($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $eventTeamMember = new EventTeamMemberEntity();
        $hydrator->hydrate($result, $eventTeamMember);

        return $eventTeamMember;
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

        if (isset($filter['event_team_id'])) {
            $where->equalTo("event_team_id", $filter['event_team_id']);
        }

        if (isset($filter['user_id'])) {
            $where->equalTo("user_id", $filter['user_id']);
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

    public function getEventTeamMembers($paginated=false, $filter = [], $order = [], $limit = null)
    {
        $select = $this->sql->select();
        $select->columns([
            'id AS event_team_member_id',
            'event_team_id',
            'user_id',
        ]);
        $select->join(
            'event_team',
            $this->tableName . ".event_team_id = event_team.id",
            [
                'name',
            ],
            $select::JOIN_INNER
        );
        $select->join(
            'event',
            "event.id = event_team.event_id",
            [
            ],
            $select::JOIN_INNER
        );

        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['user_id'])) {
            $where->equalTo($this->tableName . ".user_id", $filter['user_id']);
        }

        if (isset($filter['event_id'])) {
            $where->equalTo("event_team.event_id", $filter['event_id']);
        }

        if (isset($filter['event_team_id'])) {
            $where->equalTo("event_team.id", $filter['event_team_id']);
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

    public function getTeamMembers($paginated=false, $filter = [], $order = [], $limit = null)
    {
        $select = $this->sql->select();
        $select->columns([
            'id AS event_team_member_id',
            'event_team_id',
            'user_id',
        ]);
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

        if (isset($filter['event_team_id'])) {
            $where->equalTo("event_team_member.event_team_id", $filter['event_team_id']);
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
