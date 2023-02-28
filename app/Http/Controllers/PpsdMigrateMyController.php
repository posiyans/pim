<?php

namespace App\Http\Controllers;


use App\Models\Ppsd\PartitionPpsd;
use App\Models\Ppsd\ProtokolsPpsd;
use App\Models\Ppsd\ReportPssd;
use App\Models\Ppsd\TaskPpsd;
use App\Models\Ppsd\UserPpsd;
use App\Models\Ppsd\VReport;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class PpsdMigrateMyController extends Controller
{
    //
    public function migrate()
    {
        try {
            $a_role = Role::findByName('user');
        } catch (\Exception $e) {
            $role1 = Role::create(['name' => 'user']);
        }

        try {
            $a_role = Role::findByName('admin');
        } catch (\Exception $e) {
            $role1 = Role::create(['name' => 'admin']);
        }

        try {
            $a_role = Role::findByName('SuperAdmin');
        } catch (\Exception $e) {
            $role1 = Role::create(['name' => 'SuperAdmin']);
        }

        UserPpsd::userMigrate();
        Auth::loginUsingId(5, true);
        ProtokolsPpsd::ProtokolsMigrate();
        PartitionPpsd::PartitionMigrate();
        TaskPpsd::TaskMigrate();
        ReportPssd::TaskMigrate();
        VReport::VreportMigrate();
    }
}
