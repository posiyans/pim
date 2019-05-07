<?php

namespace App\Http\Controllers;


use App\Http\Models\Ppsd\PartitionPpsd;
use App\Http\Models\Ppsd\ProtokolsPpsd;
use App\Http\Models\Ppsd\ReportPssd;
use App\Http\Models\Ppsd\TaskPpsd;
use App\Http\Models\Ppsd\UserPpsd;
use App\Http\Models\Ppsd\VReport;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;


class PpsdMigrateController extends Controller
{
    //
    public function migrate(){

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
        Auth::loginUsingId(5, True);
        ProtokolsPpsd::ProtokolsMigrate();
        PartitionPpsd::PartitionMigrate();
        TaskPpsd::TaskMigrate();
        ReportPssd::TaskMigrate();
        VReport::VreportMigrate();
    }
}
