<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 16/05/2017
 * Time: 6:08 PM
 */

namespace csi0n\Laravel\Datatables\Repositories;


class ElementTableRepository extends BaseDatatablesRepisitory implements IDatatablesRepository
{
    protected $config;

    public function config(array $arr)
    {
        $this->config = $arr;
        return $this;
    }

    /**
     * @param array $attr
     * @return mixed
     */
    public function action(array $attr)
    {
        return $this;
    }

    /**
     * @param string $itemCallback
     * @param array $column
     * @return mixed
     */
    public function render($itemCallback = '', $column = ['*'])
    {
        if (!empty($config = $this->config) && is_array($config)) {
            if (!isset($config['columns']))
                return $this->emptyDataTables();
            $columns = $config['columns'];
            $columns = collect($columns);
            $searchColumns = $columns->where('search', true);
            $model = $this->model;
            $allowColumns = $columns->pluck('name')->toArray();
            $searchColumns->each(function ($v, $k) use (&$model, &$allowColumns) {
                if (isset($v['searchType']) && $v['searchType'] == 'input') {
                    if (!empty($searchKey = request("search.{$v['name']}")))
                        $model = $model->where($v['name'], $searchKey);
                }
            });
            if (isset($config['paginate']) && is_array($paginate = $config['paginate'])) {
                if (isset($paginate['limit'])) {
                    $model = $model->offset(intval(request('page', 1)) - 1)->limit($paginate['limit']);
                }
            }
            $models = $model->get($allowColumns);
            if ($itemCallback instanceof \Closure)
                $models->map(function ($item) use ($itemCallback) {
                    $item = $itemCallback($item);
                    return $item;
                });
            return $models;
        }
        return $this->emptyDataTables();
    }
}