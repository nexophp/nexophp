<?php

/**
 * 操作日志
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

class LogController extends \core\AdminController
{
    /**
     * 显示的日志类型
     */
    protected $type = [
        'info',
        'debug',
        'error',
        'success',
        'warning'
    ];
    /**
     * 列表 
     * @permission 操作日志.管理 操作日志.查看
     */
    public function actionIndex()
    {
        $this->view_data['select_type'] = $this->getTypes();
    }

    /**
     * 分页
     * @permission 操作日志.管理 操作日志.查看
     */
    public function actionList()
    {
        $where = [];
        $title = $this->post_data['title'] ?? '';
        $type = $this->post_data['type'] ?? '';
        $start_date = $this->post_data['start_date'] ?? '';
        $end_date = $this->post_data['end_date'] ?? '';

        if ($title) {
            $where['OR'] = [
                'title[~]' => $title,
                'content[~]' => $title
            ];
        }

        if ($type) {
            $where['type'] = $type;
        }

        if ($start_date && $end_date) {
            $start_time = strtotime($start_date . ' 00:00:00');
            $end_time = strtotime($end_date . ' 23:59:59');
            $where['created_at[>=]'] = $start_time;
            $where['created_at[<=]'] = $end_time;
        }

        $where['ORDER'] = ['id' => 'DESC'];
        $where['type'] = $this->type;
        $list = db_pager('log', '*', $where);

        // 获取用户信息
        if (!empty($list['data'])) {
            $user_ids = array_column($list['data'], 'user_id');
            $user_ids = array_filter($user_ids);
            $users = [];

            if (!empty($user_ids)) {
                $user_list = db_get_all('user', ['id', 'username'], ['id' => $user_ids]);
                foreach ($user_list as $user) {
                    $users[$user['id']] = $user['username'];
                }
            }

            foreach ($list['data'] as &$log) {
                $log['username'] = isset($log['user_id']) && isset($users[$log['user_id']]) ? $users[$log['user_id']] : '-';
                $log['created_time'] = date('Y-m-d H:i:s', $log['created_at']);
                $log['type_label'] = get_log_types($log['type']);
            }
        }

        json($list);
    }

    /**
     * 获取日志类型列表 
     */
    protected function getTypes()
    {
        $types = db_get('log', "*", ['GROUP' => 'type']);
        $result = [];
        foreach ($types as $type) {
            if (!empty($type['type'])) {
                $result[] = [
                    'value' => $type['type'],
                    'label' => get_log_types($type['type']),
                ];
            }
        }
        return $result;
    }

    /**
     * 删除日志
     * @permission 操作日志.管理 操作日志.删除
     */
    public function actionDelete()
    {
        $id = $this->post_data['id'] ?? 0;
        if (!$id) {
            json_error(['msg' => lang('参数错误')]);
        }

        db_delete('log', ['id' => $id]);
        json_success(['msg' => lang('删除成功')]);
    }

    /**
     * 清空日志
     * @permission 操作日志.管理 操作日志.删除
     */
    public function actionClear()
    {
        db_delete('log', ['id[>=]' => 1, 'type' => $this->type]);
        json_success(['msg' => lang('清空成功')]);
    }
}
