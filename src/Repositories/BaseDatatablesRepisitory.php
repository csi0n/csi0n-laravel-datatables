<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 16/05/2017
 * Time: 6:11 PM
 */

namespace csi0n\Laravel\Datatables\Repositories;


use Closure;

class BaseDatatablesRepisitory
{
    protected $model;

    protected $columns = [];

    protected $actionSetting = [];

    protected $returnEmpty = false;

    protected $global_data = [];

    protected $afterQuery = null;

    protected $limit = [];

    public function limit($limit)
    {
        if (!is_array($limit)) {
            throw  new \Exception("datatables limit must be array");
        }
        $this->limit = $limit;

        return $this;
    }

    public function globalData($global)
    {
        $this->global_data = $global;

        return $this;
    }

    /**
     * @desc 运行datatables之前先检查一下条件
     * true的时候通过检查，
     * false则直接返回空datatables
     *
     * @param $function
     *
     * @return $this|array
     */
    public function check($function)
    {
        if ($function instanceof Closure) {
            if ($function()) {
                return $this;
            }
        }
        $this->returnEmpty = true;

        return $this;
    }

    /**
     * @desc 设置datatables模型
     *
     * @param $model
     *
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @desc初始化查询条件
     *
     * @param $function
     *
     * @return $this
     */
    public function beforeQuery($function)
    {
        if ($function instanceof Closure) {
            $model = $this->model;
            $this->model = $function($model);
        }

        return $this;
    }


    /**
     * @desc columns的一些配置信息
     *
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function emptyDataTables()
    {
        return $this->getConfig('emptyData');
    }

    protected function getConfig($key)
    {
        return config("datatables.{$key}");
    }
}