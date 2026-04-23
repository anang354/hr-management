<?php

return [
  'title' => '請假申請',
  'plural_label' => '請假申請',
  'navigation_label' => '請假申請',
  'fields' => [
    'employee_id' => '員工',
    'user_id' => '提交人',
    'letter_number' => '信件編號',
    'leave_type' => '請假類型',
    'start_date' => '開始日期',
    'end_date' => '結束日期',
    'leave_session' => '請假時段',
    'total_days' => '總天數',
    'reason' => '原因',
    'status' => '狀態',
    'rejected_by' => '拒絕人',
    'manager_id' => '經理',
    'hr_id' => 'HR',
    'rejection_note' => '拒絕原因',
  ],
  'status' => [
    'all' => '全部',
    'pending' => '待處理',
    'manager_approved' => '經理批准',
    'approved' => '批准',
    'rejected' => '拒絕',
  ],
  'actions' => [
    'detail' => '詳細',
    'approve' => '批准',
    'reject' => '拒絕',
    'print' => '列印',
  ],
  'contents' => [
    'submitted_by' => '提交人',
    'reason_for_rejection' => '拒絕原因',
    'days' => '天',
  ],
  'notifications' => [
    'request_rejected' => '申請已被拒絕',
    'request_approved' => '申請已被批准'
  ],
  'leave_type' => [
    'annual' => '年假',
    'sick' => '病假',
    'personal' => '事假',
    'maternity' => '產假',
    'marriage' => '婚假',
  ],
  'leave_session' => [
    'fullday' => '全天',
    'halfday' => '半天',
  ]
];
