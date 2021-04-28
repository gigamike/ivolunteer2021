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
use Event\Model\EventEntity;

class EventMapper
{
    protected $tableName = 'event';
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

        if (isset($filter['name'])) {
            $where->equalTo("name", $filter['name']);
        }

        if (isset($filter['date_greater_than'])) {
            $where->greaterThan("event_date", $filter['date_greater_than']);
        }

        if (isset($filter['id_not'])) {
            $where->notEqualTo("id", $filter['id_not']);
        }

        if (isset($filter['name'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like("name", "%" . $filter['name_keyword'] . "%")
            );
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
            $entityPrototype = new EventEntity();
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

            $entityPrototype = new EventEntity();
            $hydrator = new ClassMethods();
            $resultset = new HydratingResultSet($hydrator, $entityPrototype);
            $resultset->initialize($results);
        }

        return $resultset;
    }

    public function save(EventEntity $event)
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

    public function getEvent($id)
    {
        $select = $this->sql->select();
        $select->where(['id' => $id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        if (!$result) {
            return null;
        }

        $hydrator = new ClassMethods();
        $event = new EventEntity();
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

    public function getEvents($paginated=false, $filter = [], $order = [])
    {
        $select = $this->sql->select();
        $select->columns([
            'id',
            'category_id',
            'name',
            'organization',
            'description',
            'venue',
            'city',
            'latitude',
            'longitude',
            'event_date',
            'event_type',
            'volunteer_limit',
            'contact_name',
            'contact_email',
            'contact_mobile_no',
            'website_url',
            'created_datetime',
            /*
            'distance' => new \Zend\Db\Sql\Expression("SQRT(POW(69.1 * (user_location.latitude - " . $filter['latitude'] . "), 2)
                    + POW(69.1 * (" . $filter['longitude'] . " - user_location.longitude) * COS(user_location.latitude / 57.3), 2))"),
            */
            // Note: The provided distance is in Miles. If you need Kilometers, use 6371 instead of 3959.
            'distance' => new \Zend\Db\Sql\Expression("( 6371 * acos( cos( radians(" . $filter['latitude'] . ") ) * cos( radians( event.latitude ) )
                   * cos( radians(event.longitude) - radians(" . $filter['longitude'] . ")) + sin(radians(" . $filter['latitude'] . "))
                   * sin( radians(event.latitude))))"),
        ]);
        $select->join(
            'category',
            $this->tableName . ".category_id = category.id",
            [
                'category',
            ],
            $select::JOIN_INNER
        );

        $where = new \Zend\Db\Sql\Where();

        if (isset($filter['id'])) {
            $where->equalTo($this->tableName . ".id", $filter['id']);
        }

        if (isset($filter['category_id']) && !empty($filter['category_id'])) {
            $where->equalTo($this->tableName . ".category_id", $filter['category_id']);
        }

        if (isset($filter['event_date']) && !empty($filter['event_date'])) {
            $where->equalTo($this->tableName . ".event_date", $filter['event_date']);
        }

        if (isset($filter['distance']) && !empty($filter['distance'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Expression("( 6371 * acos( cos( radians(" . $filter['latitude'] . ") ) * cos( radians( event.latitude ) )
                   * cos( radians(event.longitude) - radians(" . $filter['longitude'] . ")) + sin(radians(" . $filter['latitude'] . "))
                   * sin( radians(event.latitude)))) <= " . $filter['distance'])
            );
        }

        if (isset($filter['name'])) {
            $where->equalTo($this->tableName . ".name", $filter['name']);
        }

        if (isset($filter['date_greater_than'])) {
            $where->greaterThan($this->tableName . ".event_date", $filter['date_greater_than']);
        }

        if (isset($filter['id_not'])) {
            $where->notEqualTo($this->tableName . ".id", $filter['id_not']);
        }

        if (isset($filter['name_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like($this->tableName . ".name", "%" . $filter['name_keyword'] . "%")
            );
        }

        if (isset($filter['organization_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like($this->tableName . ".organization", "%" . $filter['organization_keyword'] . "%")
            );
        }

        if (isset($filter['city_keyword'])) {
            $where->addPredicate(
                new \Zend\Db\Sql\Predicate\Like($this->tableName . ".city", "%" . $filter['city_keyword'] . "%")
            );
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
