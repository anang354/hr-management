<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Leave Request</title>
  <style>
    @font-face {
      font-family: 'Noto Sans SC';
      font-style: normal;
      font-weight: normal;
      src: url("{{ storage_path('fonts/NotoSansSC-Regular.ttf') }}") format('truetype');
    }

    @font-face {
      font-family: 'Noto Sans SC Bold';
      font-style: normal;
      font-weight: bold;
      src: url("{{ storage_path('fonts/NotoSansSC-Bold.ttf') }}") format('truetype');
    }

    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Noto Sans SC';
    }

    p {
      font-size: 11px;
      margin: 3px 0;
    }

    h1 {
      font-size: 16pt;
      margin: 5px 0;
    }

    h4,
    h5,
    h6 {
      margin: 5px 0;
      font-family: 'Noto Sans SC Bold';
    }

    table tr {
      height: 5px;
    }

    .text-end {
      text-align: right;
    }

    .bordered-table {
      margin-top: 10px;
      font-size: 12px;
      width: 100%;
      border-collapse: collapse;
    }

    .bordered-table tr,
    .bordered-table td {
      border: 1px solid #000;
      padding: 5px;
    }

    .text-bold {
      font-weight: bold;
      font-family: 'Noto Sans SC Bold';
    }

    .text-center {
      text-align: center;
    }

    .my-2 {
      margin-top: 10px;
      margin-bottom: 10px;
    }

    ul li p,
    ol li p {
      margin: 0;
      padding: 0;
    }

    .date_sign {
      width: 100%;
      text-align: right;
    }

    .kop-surat {
      width: 100%;
      padding: 10px 0;
    }

    .kop-surat img {
      float: left;
    }

    .kop-surat h1 {
      font-size: 16pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
    }
  </style>
</head>

<body>
  <div class="kop-surat">
    <img src="{{ $image }}" width="72px" alt="">
    <div class="text-center">
      <h1>PT. DUNIA SOLAR INDONESIA</h1>
      <h4 style="font-size: 14px;">请假申请表 Leave request form</h4>
    </div>
  </div>
  <div style="clear:both"></div>
  <table class="bordered-table">
    <tr>
      <td>行政 Department</td>
      <td>
        <p>
          {{ $leaveRequest->employee->department->getTranslation('name', 'zh_HK') }}
          {{ $leaveRequest->employee->department->getTranslation('name', 'en') }}
        </p>
      </td>
      <td>姓名 Name</td>
      <td colspan="2">
        <p class="text-bold">{{ $leaveRequest->employee->name }}</p>
      </td>
    </tr>
    <tr>
      <td>工号 Work No.</td>
      <td>
        <p class="text-bold">{{ $leaveRequest->employee->employee_number }}</p>
      </td>
      <td>职位 Position</td>
      <td colspan="2">
        <p class="text-bold">{{ $leaveRequest->employee->job }}</p>
      </td>
    </tr>
    <tr>
      <td rowspan="2">
        <p>请假类型 Leave Type:</p>
        <p class="text-bold">{{ $leaveRequest->leave_type->getDualLabel() }}</p>
      </td>
      <td>
        <p>休假开始日期</p>
        <p>Leave start date : {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d-m-Y') }}</p>
      </td>
      <td>
        <p>开始时间</p>
        <p>Start Time: </p>
      </td>
      <td rowspan="2" colspan="2" class="text-center">
        <p>请假天数 Leave Days</p>
        <p style="font-weight: bold; font-size: 14px;">{{ $leaveRequest->total_days }}</p>
      </td>
    </tr>
    <tr>
      <td>
        <p>休假结束日期</p>
        <p>Leave end date : {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d-m-Y') }}</p>
      </td>
      <td>
        <p>结束时间</p>
        <p>End Time: </p>
      </td>
    </tr>
    <tr>
      <td>
        <p>请假原因</p>
        <p>Reason for leave</p>
      </td>
      <td colspan="4">{{ $leaveRequest->reason }}</td>
    </tr>
    <tr>
      <td>
        <p>工作交接</p>
        <p>Handover matters</p>
      </td>
      <td></td>
      <td>
        <p>联系方式 Contact information</p>
      </td>
      <td colspan="2"></td>
    </tr>
    <tr class="text-center">
      <td>
        <p>确认</p>
        <p>Confirm</p>
      </td>
      <td>
        <p>总经理</p>
        <p>General Manager</p>
      </td>
      <td>
        <p>人力资源部</p>
        <p>HR Department</p>
      </td>
      <td>
        <p>部门主管</p>
        <p>Department Head</p>
      </td>
      <td>
        <p>申请人</p>
        <p>Applicant</p>
      </td>
    </tr>
    <tr class="text-center" style="height: 100px;">
      <td>
        <p>签名</p>
        <p>Signature</p>
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td style="vertical-align: bottom;"></td>
    </tr>
    <tr>
      <td colspan="5">
        <p>阐明 Illustrate:</p>
        <p>1. 请假申请流程按照公司规定执行。 Leave application procedures are handled according to the company's</p>
        <p>2. 部门员工的请假须经部门主管批准，超过七天的请假须经公司总经理批准。部门主管的请假须经总经理批准。Department employee's leave must be approved by the
          department head, and leave of more than seven days approved by the company's general manager. The department
          head's leave must be approved by the general manager</p>
        </p>
      </td>
    </tr>
  </table>
  <div style="width: 100%; text-align: right;">
    <p>{{ $leaveRequest->letter_number }}</p>
  </div>
</body>

</html>
