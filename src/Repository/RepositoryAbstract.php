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

        // init PDO
        $this->pdo = new \PDO('mysql:host=' . $this->settings['db']['host'] . ';dbname=' . $this->settings['db']['database'] . ';charset=utf8', $this->settings['db']['username'], $this->settings['db']['password']);

        // init tables
        if (file_exists(__DIR__ . '/../Configuration/Sql/' . $this->table. '.sql')) {
            $databaseQuery = file_get_contents(__DIR__ . '/../Configuration/Sql/' . $this->table . '.sql');
            $this->pdo->exec($databaseQuery);
        }
    }


    /**
     * insert
     *
     * @param \Madj2k\TwitterAnalyser\Model\ModelAbstract $model
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function insert (\Madj2k\TwitterAnalyser\Model\ModelAbstract $model)
    {
        if (! $model instanceof $this->model) {
            throw new RepositoryException('Given object not handled by this repository.');
        }

        // set defaults
        $model->setCreateTimestamp(time())->setChangeTimestamp(time());

        $insertProperties = $properties = $this->getPropertiesOfModel($model);
        unset($insertProperties['uid']);
        if (count($insertProperties) > 0) {

            $columns = implode(',', array_keys($insertProperties));
            $placeholder = implode(',', array_fill(0, count($insertProperties), '?'));
            $values = array_values($insertProperties);

            $sql = 'INSERT INTO ' . $this->table . ' (' . $columns . ') VALUES (' . $placeholder . ')';
            $sth = $this->pdo->prepare($sql);

            if ($result = $sth->execute($values)) {
                $model->setUid($this->pdo->lastInsertId());
                return $result;
            } else {
                $error = $sth->errorInfo();
                throw new RepositoryException($error[2]);
            }
        }

        return false;
    }


    /**
     * update
     *
     * @param \Madj2k\TwitterAnalyser\Model\ModelAbstract $model
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function update (\Madj2k\TwitterAnalyser\Model\ModelAbstract $model)
    {
        if (! $model instanceof $this->model) {
            throw new RepositoryException('Given object not handled by this repository.');
        }

        if (! $model->getUid()) {
            throw new RepositoryException('No uid given.');
        }

        // set defaults
        $model->setChangeTimestamp(time());

        $updateProperties = $properties = $this->getPropertiesOfModel($model);
        unset($updateProperties['uid']);
        if (count($updateProperties) > 0) {

            $columns = implode(' = ?,', array_keys($updateProperties)) . '= ?';
            $values = array_values($updateProperties);
            $values[] = $model->getUid();

            $sql = 'UPDATE ' . $this->table . ' SET ' . $columns . ' WHERE uid = ?';
            $sth = $this->pdo->prepare($sql);

            if ($result = $sth->execute($values)) {
                return $result;
            } else {
                $error = $sth->errorInfo();
                throw new RepositoryException($error[2]);
            }
        }

        return false;
    }


    /**
     * @param \Madj2k\TwitterAnalyser\Model\ModelAbstract $model
     * @return array
     */
    protected function getPropertiesOfModel (\Madj2k\TwitterAnalyser\Model\ModelAbstract $model)
    {
        // go through all getter-methods and find relevant properties
        $properties = [];
        $allMethods = get_class_methods ($model);
        foreach ($allMethods as $method) {
            if (strpos($method, 'get') === 0) {
                $property = GeneralUtility::camelCaseToUnderscore(substr($method, 3));
                $properties[$property] = $model->$method();
            }
        }

        return $properties;
    }

}