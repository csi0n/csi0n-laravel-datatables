<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 16/05/2017
 * Time: 6:08 PM
 */

namespace csi0n\Laravel\Datatables\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

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
            if (!isset($config['columns'])) {
                return $this->emptyDataTables();
            }

            $columns       = $config['columns'];
            $columns       = collect($columns);
            $searchColumns = $columns->where('search', true);
            $model         = $this->model;
            $allowColumns  = $columns->pluck('name')->toArray();
            $searchColumns->each(function ($v, $k) use (&$model, &$allowColumns) {
                if (isset($v['searchType']) && $v['searchType'] == 'input') {
                    if (!empty($search = request('search'))) {
                        $search = json_decode($search, true);
                        if (isset($search[$v['name']]) && !empty($searchKey = $search[$v['name']])) {
                            $model = $model->where($v['name'], $searchKey);
                        }
                    }
                }
            });
            if (isset($config['paginate']) && is_array($paginate = $config['paginate'])) {
                if (isset($paginate['page']) && is_int($paginate['page'])) {
                    $models = $model->paginate($paginate['page']);
                } else {
                    $models = $model->paginate(20);
                }
            } else {
                $models = $model->paginate(20);
            }
            if ($models instanceof LengthAwarePaginator) {
                $models->setCollection($this->itemCallback($itemCallback, $models->getCollection()));
            }
            $renderColumns = collect(['render_columns' => $this->getConfigColumns()]);
            return $renderColumns->merge($models);
        }
        return $this->emptyDataTables();
    }
    private function getConfigColumns()
    {
        if (!isset($this->config['columns'])) {
            return [];
        }
        return $this->config['columns'];
    }
    private function itemCallback($itemCallback, $models)
    {
        if ($itemCallback instanceof \Closure) {
            $config = $this->config;
            $models->map(function ($item) use ($itemCallback, $config) {
                $item = $itemCallback($item);
                if (!is_null($item) && $columns = $this->getConfigColumns()) {
                    $array = [];

                    // $item->getRelations()
                    $renderColumns = array_keys($columns);
                    $array         = array();
                    foreach ($renderColumns as $key => $value) {
                        array_set($array, $value, '');
                    }
                    $relations = $item->getRelations();
                    foreach ($relations as $key => &$value) {
                        if (isset($array[$key])) {
                            $value->setVisible(array_keys($array[$key]));
                        }
                    }
                    $item->setVisible(array_merge($renderColumns, array_keys($relations)));
                }
                return $item;
            });
        }
        return $models;
    }
}
