<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 5/14/17
 * Time: 5:40 PM
 */

namespace csi0n\Laravel\Datatables\Repositories;

use Closure;

class CLaravelDatatablesRepository extends BaseDatatablesRepisitory implements IDatatablesRepository
{
    /**
     * @desc actionButton配置
     *
     * @param array $attr
     *
     * @return $this
     */
    public function action(array $attr)
    {
        if (isset($attr['actionPermission'])) {
            if (auth()->user()->can(config($attr['actionPermission']))) {
                unset($attr['actionPermission']);
                $this->actionSetting = $attr;
            }
            return $this;
        }
        $this->actionSetting = $attr;
        return $this;
    }

    /**
     * @desc 渲染
     *
     * @param string $itemCallback
     * @param array $column
     *
     * @return array
     */
    public function render($itemCallback = '', $column = ['*'])
    {
        if ($this->returnEmpty) {
            return $this->emptyDataTables();
        }
        $draw = request('draw', $this->getConfig('default.draw')); /*获取请求次数*/
        $start = request('start', $this->getConfig('default.start')); /*获取开始*/
        $length = request('length', $this->getConfig('default.length')); ///*获取条数*/
        $search_pattern = request('search.regex', $this->getConfig('default.searchRegex')); /*是否启用模糊搜索*/
        $columns = collect($this->columns);
        $search_columns = $columns->where('search', true);
        $search_columns->each(function ($item) use ($search_pattern) {
            if (isset($item['has']) && $item['has']) {

            } else {
                $request_item = request($item['name'], '');
                if ($request_item) {
                    if ($item['name'] == 'status') {
                        $this->model = $this->model->where('status', $request_item);
                    } else {
                        if ($search_pattern) {
                            $this->model = $this->model->where($item['name'], "like", "%{$request_item}%");
                        } else {
                            $this->model = $this->model->where($item['name'], $request_item);
                        }
                    }
                }
            }

        });
        $model = $this->model;
        $limit_array = $this->limit;
        if (is_array($limit_array)) {
//            限制搜索条数
            if (isset($limit_array['max']) && isset($limit_array['orderBy'])) {
                $maxModel = $model;
                $tempOrders = $maxModel->getQuery()->orders;
                $maxModel->getQuery()->orders = null;
                $maxModel = $maxModel
                    ->orderBy($limit_array['orderBy'], 'asc')
                    ->take($limit_array['max'])
                    ->get([$limit_array['orderBy']]);
                if (!$maxModel->isEmpty()) {
                    $max_model_order_by = $maxModel
                        ->max($limit_array['orderBy']);
                    $model = $this->model->where($limit_array['orderBy'], '<=', $max_model_order_by);
                }
                $model->getQuery()->orders = $tempOrders;
            }
        }
        $count = $model->count();
        $model = $model->offset($start)->limit($length);
        $model = $model->get($column);
        $actionButtonSetting = $this->actionSetting;
        $events = [];
        if (isset($actionButtonSetting['events'])) {
            $events = $actionButtonSetting['events'];
            unset($actionButtonSetting['events']);
        }
        $model->each(function ($item) use (
            $actionButtonSetting, $itemCallback, $events
        ) {
            if ($itemCallback instanceof Closure) {
                $item = $itemCallback($item);
            }
            $actionButton = [];
            if (!empty($events)) {
                foreach ($events as $k => $v) {
                    if (isset($item[$k])) {
                        if ($v['type'] == 'mutually_exclusive') {
                            if (!isset($v['data_type']) || $v['data_type'] == 'default') {
                                $data = $v['data'];
                                foreach ($data as $kk => $vv) {
                                    if ($item[$k] == $kk) {
                                        $this->buildActionButton($actionButton, $vv, $item);
                                    }
                                }
                            }
                        } elseif ($v['type'] == 'coexist') {
                            $data = $v['data'];
                            foreach ($data as $kk => $vv) {
                                if ($item[$k] == explode('|', $kk)[1]) {
                                    $this->buildActionButton($actionButton, $vv, $item);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($actionButtonSetting as $value) {
                $this->buildActionButton($actionButton, $value, $item);
            }
            $item['actionButton'] = $actionButton;
        });

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $model,
        ];
    }

    private function buildActionButton(&$actionButton, $value, $item)
    {
        if (auth()->user()->can(config($value['slug']))) {
            $url = '';
            if (isset($value['route'])) {
                if ($value['route'] instanceof Closure) {
                    $url = $value['route']($item);
                } else {
                    list($route, $data) = explode('&', $value['route']);
                    preg_match_all("/{.*?}/", $data, $matchs);
                    $parameters = [];
                    foreach ($matchs as $match) {
                        foreach ($match as $v) {
                            $r = str_replace(["{", "}"], ["", ""], $v);
                            $parameters[$r] = $item[$r];
                        }
                    }
                    preg_match_all("/-.*?-/", $data, $m);
                    foreach ($m as $key) {
                        foreach ($key as $kk) {
                            $r = str_replace(["-"], [""], $kk);
                            if (!is_null($this->global_data[$r])) {
                                $parameters[$r] = $this->global_data[$r];
                            }
                        }
                    }
                    $url = call_user_func_array(config('datatables.function.route'), [$route, $parameters]);
                }
            }
            array_push($actionButton, [
                'name_zh' => $value['name_zh'],
                'type' => $value['type'],
                'url' => $url,
            ]);
        }
    }
}