<?php
namespace Madj2k\TwitterAnalyser\Repository;
use Madj2k\TwitterAnalyser\Utility\GeneralUtility;


/**
 * RepositoryAbstract
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

abstract class RepositoryAbstract
{

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var array
     */
    protected $settings;


    /**
     * Constructor
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->table = GeneralUtility::getTableNameFromRepository($this);
        $this->model = GeneralUtility::getModelClassFromRepository($this);

        // init PDO with utf8mb4 for emoticons
        $this->pdo = new \PDO(
            'mysql:host=' . $this->settings['db']['host'] . ';dbname=' . $this->settings['db']['database'] . ';charset=utf8',
            $this->settings['db']['username'],
            $this->settings['db']['password'],
            array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            )
        );

        // init tables
        if (file_exists(__DIR__ . '/../Configuration/Sql/' . $this->table. '.sql')) {
            $databaseQuery = file_get_contents(__DIR__ . '/../Configuration/Sql/' . $this->table . '.sql');
            $this->pdo->exec($databaseQuery);
        }
    }


    /**
     * Magic function for default queries
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     *  @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function __call(string $method, array $arguments)
    {
        $whereArguments = [];
        $whereClause = '1 = 1';
        $fetchMethod = '_findAll';

        if (strpos($method, 'findOneBy') === 0) {
            if (! $arguments[0]) {
                throw new RepositoryException(sprintf('Method %s expects one parameter as filter criterium.', $method));
            }

            $property = substr($method, 9);
            $fetchMethod = '_findOne';
            $whereArguments = array($arguments[0]);
            $whereClause = GeneralUtility::camelCaseToUnderscore($property) . ' = ?';

        } elseif (strpos($method, 'findBy') === 0) {
            if (! $arguments[0]) {
                throw new RepositoryException(sprintf('Method %s expects one parameter as filter criterium.', $method));
            }

            $property = (substr($method, 6));
            $whereArguments = array($arguments[0]);
            $whereClause = GeneralUtility::camelCaseToUnderscore($property) . ' = ?';

        } elseif (strpos($method, 'findAll') === 0) {
            // nothing to do

        } else {
            throw new RepositoryException(sprintf('The %s repository does not have a method %s.', get_called_class(), $method));
        }

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $whereClause;
        return $this->$fetchMethod($sql, $whereArguments);
        //===
    }


    /**
     *
     * @param string $sql
     * @param array $arguments
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function _findAll (string $sql, array $arguments = [])
    {

        $sth = $this->pdo->prepare($sql);
        if ($sth->execute($arguments)) {
            if ($resultDb = $sth->fetchAll(\PDO::FETCH_ASSOC)) {

                $result = [];
                foreach($resultDb as $column) {
                    $result[] = new $this->model($column);
                }
                return $result;
                //===

            };
            return null;
            //===

        } else {
            throw new RepositoryException($sth->errorInfo()[2]);
            //===
        }
    }


    /**
     *
     * @param string $sql
     * @param array $arguments
     * @return \Madj2k\TwitterAnalyser\Model\ModelAbstract|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function _findOne (string $sql, array $arguments = [])
    {

        $sql .= ' LIMIT 1';

        $sth = $this->pdo->prepare($sql);
        if ($sth->execute($arguments)) {
            if ($resultDb = $sth->fetch(\PDO::FETCH_ASSOC)) {

                return new $this->model($resultDb);
                //===
            };

            return null;
            //===

        } else {
            throw new RepositoryException($sth->errorInfo()[2]);
            //===
        }


    }



    /**
     * insert
     *
     * @param \Madj2k\TwitterAnalyser\Model\ModelAbstract $model
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function insert(\Madj2k\TwitterAnalyser\Model\ModelAbstract $model)
    {
        if (! $model instanceof $this->model) {
            throw new RepositoryException('Given object not handled by this repository.');
            //===
        }

        $insertProperties = $model->_getChangedProperties();
        if (count($insertProperties) > 0) {

            // set defaults
            $insertProperties['create_timestamp'] = $insertProperties['change_timestamp'] = time();

            $columns = implode(',', array_keys($insertProperties));
            $placeholder = implode(',', array_fill(0, count($insertProperties), '?'));
            $values = array_values($insertProperties);

            $sql = 'INSERT INTO ' . $this->table . ' (' . $columns . ') VALUES (' . $placeholder . ')';
            $sth = $this->pdo->prepare($sql);

            if ($result = $sth->execute($values)) {
                $model->setUid($this->pdo->lastInsertId());
                return $result;
                //===
            } else {
                $error = $sth->errorInfo();
                throw new RepositoryException($error[2]);
                //===
            }
        }

        return false;
        //===
    }


    /**
     * update
     *
     * @param \Madj2k\TwitterAnalyser\Model\ModelAbstract $model
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function update(\Madj2k\TwitterAnalyser\Model\ModelAbstract $model)
    {
        if (! $model instanceof $this->model) {
            throw new RepositoryException('Given object not handled by this repository.');
            //===
        }

        if ($model->_isNew()) {
            throw new RepositoryException('Given object is not persisted and therefore can not be updated.');
            //===
        }

        $updateProperties = $model->_getChangedProperties();
        if (count($updateProperties) > 0) {

            // set defaults
            $updateProperties['change_timestamp'] = time();

            $columns = implode(' = ?,', array_keys($updateProperties)) . '= ?';
            $values = array_values($updateProperties);
            $values[] = $model->getUid();

            $sql = 'UPDATE ' . $this->table . ' SET ' . $columns . ' WHERE uid = ?';
            $sth = $this->pdo->prepare($sql);

            if ($result = $sth->execute($values)) {
                return $result;
                //===
            } else {
                $error = $sth->errorInfo();
                throw new RepositoryException($error[2]);
                //===
            }
        }

        return false;
        //===
    }



}