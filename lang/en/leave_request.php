<?php

return [
  'title' => 'Leave Request',
  'plural_label' => 'Leave Requests',
  'navigation_label' => 'Leave Request',
  'fields' => [
    'employee_id' => 'Employee',
    'user_id' => 'Submited by',
    'letter_number' => 'Letter Number',
    'leave_type' => 'Leave Type',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'total_days' => 'Total Days',
    'leave_session' => 'Leave Session',
    'reason' => 'Reason',
    'status' => 'Status',
    'rejected_by' => 'Rejected By',
    'rejection_note' => 'Rejection Note',
  ],
  'status' => [
    'all' => 'All',
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
  ],
  'actions' => [
    'detail' => 'Detail',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'print' => 'Print',
  ],
  'contents' => [
    'submitted_by' => 'Submitted by',
    'reason_for_rejection' => 'Reason for rejection',
    'days' => 'Days',
  ],
  'notifications' => [
    'request_rejected' => 'Request has been rejected',
    'request_approved' => 'Request has been approved'
  ],
  'leave_type' => [
    'annual' => 'Annual',
    'sick' => 'Sick',
    'personal' => 'Personal',
    'maternity' => 'Maternity',
    'marriage' => 'Marriage',
  ],
  'leave_session' => [
    'fullday' => 'Full Day',
    'halfday' => 'Half Day',
  ]
];
