<?php

return [
  'title' => 'Overtime Request',
  'plural_label' => 'Overtime Requests',
  'navigation_label' => 'Overtime Request',
  'navigation_group' => 'Overtime',
  'fields' => [
    'user_id' => 'Submited by',
    'overtime_date' => 'Overtime Date',
    'department' => 'Department',
    'content' => 'Content',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'status' => 'Status',
    'employees_items' => 'Number of Employees',
    'reason' => 'Reason',
    'rejected_by' => 'Rejected By',
  ],
  'status' => [
    'all' => 'All',
    'pending' => 'Pending',
    'spv_approved' => 'SPV Approved',
    'manager_approved' => 'Manager Approved',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
  ],
  'actions' => [
    'detail' => 'Detail',
    'edit' => 'Edit',
    'approve' => 'Approve',
    'reject' => 'Reject',
  ],
  'contents' => [
    'submitted_by' => 'Submitted by',
    'reason_for_rejection' => 'Reason for rejection'
  ],
  'notifications' => [
    'request_rejected' => 'Request has been rejected',
    'request_approved' => 'Request has been approved'
  ]
];
