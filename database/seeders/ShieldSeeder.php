<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:User","View:User","Create:User","Update:User","Delete:User","ViewAny:Role","View:Role","Create:Role","Update:Role","Delete:Role","ViewAny:Activity","View:Activity","Create:Activity","Update:Activity","Delete:Activity","View:MyProfilePage","View:ListLogs","View:OverlookWidget","View:LatestAccessLogs","ViewAny:AcademicYear","View:AcademicYear","Create:AcademicYear","Update:AcademicYear","Delete:AcademicYear","ViewAny:AttendanceDetail","View:AttendanceDetail","Create:AttendanceDetail","Update:AttendanceDetail","Delete:AttendanceDetail","ViewAny:Attendance","View:Attendance","Create:Attendance","Update:Attendance","Delete:Attendance","ViewAny:Grade","View:Grade","Create:Grade","Update:Grade","Delete:Grade","ViewAny:LeaveRequest","View:LeaveRequest","Create:LeaveRequest","Update:LeaveRequest","Delete:LeaveRequest","ViewAny:Major","View:Major","Create:Major","Update:Major","Delete:Major","ViewAny:Student","View:Student","Create:Student","Update:Student","Delete:Student","View:Presence","View:Report","View:SubmitPresence"]},{"name":"operator","guard_name":"web","permissions":["ViewAny:User","View:User","Create:User","Update:User","Delete:User","View:MyProfilePage","View:OverlookWidget","ViewAny:AcademicYear","View:AcademicYear","Create:AcademicYear","Update:AcademicYear","Delete:AcademicYear","ViewAny:AttendanceDetail","View:AttendanceDetail","Create:AttendanceDetail","Update:AttendanceDetail","Delete:AttendanceDetail","ViewAny:Attendance","View:Attendance","Create:Attendance","Update:Attendance","Delete:Attendance","ViewAny:Grade","View:Grade","Create:Grade","Update:Grade","Delete:Grade","ViewAny:LeaveRequest","View:LeaveRequest","Create:LeaveRequest","Update:LeaveRequest","Delete:LeaveRequest","ViewAny:Major","View:Major","Create:Major","Update:Major","Delete:Major","ViewAny:Student","View:Student","Create:Student","Update:Student","Delete:Student","View:Presence","View:Report","View:SubmitPresence"]},{"name":"guru","guard_name":"web","permissions":["ViewAny:User","View:User","ViewAny:AcademicYear","View:AcademicYear","ViewAny:AttendanceDetail","View:AttendanceDetail","ViewAny:Attendance","View:Attendance","ViewAny:Grade","View:Grade","ViewAny:LeaveRequest","View:LeaveRequest","ViewAny:Major","View:Major","ViewAny:Student","View:Student","View:Presence","View:Report","View:SubmitPresence"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
