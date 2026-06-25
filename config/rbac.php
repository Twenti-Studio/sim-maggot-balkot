<?php

return [
    'roles' => [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin/Pengelola',
        'operator' => 'Petugas Operasional',
        'treasurer' => 'Bendahara/Keuangan',
    ],

    'permissions' => [
        'super_admin' => ['*'],
        'admin' => [
            'production.view', 'production.create', 'production.update', 'production.delete', 'production.validate',
            'attendance.create', 'attendance.view', 'staff.manage',
            'asset.view', 'asset.create', 'asset.update', 'maintenance.create',
            'report.export', 'location.manage', 'finance.view', 'finance.validate',
        ],
        'operator' => [
            'production.view', 'production.create', 'production.update',
            'attendance.create', 'attendance.view',
            'asset.view', 'asset.update', 'maintenance.create',
        ],
        'treasurer' => [
            'attendance.view', 'payroll.manage',
            'finance.view', 'finance.create', 'finance.update',
            'report.export',
        ],
    ],
];
