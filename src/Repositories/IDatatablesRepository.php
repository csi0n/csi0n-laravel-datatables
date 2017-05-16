<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 16/05/2017
 * Time: 6:01 PM
 */

namespace csi0n\Laravel\Datatables\Repositories;


interface IDatatablesRepository
{
    /**
     * @param $limit
     * @return mixed
     */
    public function limit($limit);

    /**
     * @param $global
     * @return mixed
     */
    public function globalData($global);

    /**
     * @param $callback
     * @return mixed
     */
    public function check($callback);

    /**
     * @param array $attr
     * @return mixed
     */
    public function action(array $attr);

    /**
     * @param $model
     * @return mixed
     */
    public function model($model);

    /**
     * @param $callback
     * @return mixed
     */
    public function beforeQuery($callback);

    /**
     * @param array $columns
     * @return mixed
     */
    public function columns(array $columns);

    /**
     * @param string $itemCallback
     * @param array $column
     * @return mixed
     */
    public function render($itemCallback = '', $column = ['*']);

    /**
     * @return mixed
     */
    public function emptyDataTables();
}