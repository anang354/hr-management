<?php

return [
  'title' => '加班申請',
  'plural_label' => '加班申請',
  'navigation_label' => '加班申請',
  'navigation_group' => '加班',
  'fields' => [
    'user_id' => '提交人',
    'overtime_date' => '加班日期',
    'department' => '部门',
    'content' => '內容',
    'status' => '狀態',
    'approved' => '已获批准',
    'rejected' => '拒绝',
    'employees_items' => '员工人数',
    'reason' => '原因',
    'rejected_by' => '拒绝人',
  ],
  'status' => [
    'all' => '全部',
    'pending' => '待審批',
    'spv_approved' => '主管批准',
    'manager_approved' => '经理批准',
    'approved' => '批准',
    'rejected' => '拒絕',
  ],
  'actions' => [
    'detail' => '详情',
    'edit' => '编辑',
    'approve' => '批准',
    'reject' => '拒绝',
  ],
  'contents' => [
    'submitted_by' => '提交人',
    'reason_for_rejection' => '拒绝原因'
  ],
  'notifications' => [
    'request_rejected' => '请求已被拒绝',
    'request_approved' => '请求已被批准'
  ]
];
