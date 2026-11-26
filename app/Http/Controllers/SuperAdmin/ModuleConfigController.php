<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformModuleConfig;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleConfigController extends Controller
{
    public function index()
    {
        return redirect()->route('super_admin.settings.index', ['tab' => 'modules']);
    }

    public function toggleGlobal(Request $request, $id)
    {
        $module = PlatformModuleConfig::findOrFail($id);
        $oldState = $module->is_enabled_globally;
        $module->is_enabled_globally = !$oldState;
        $module->save();

        AuditLogger::log(
            'TOGGLE_MODULE_GLOBAL',
            'Super Admin Platform',
            $module->id,
            "Super Admin (" . Auth::user()->name . ") mengubah status global modul {$module->module_name} menjadi " . ($module->is_enabled_globally ? 'AKTIF' : 'NONAKTIF'),
            ['old_state' => $oldState],
            ['new_state' => $module->is_enabled_globally],
            null,
            'success',
            'CONFIG_CHANGE'
        );

        $stateText = $module->is_enabled_globally ? 'diaktifkan secara global' : 'dinonaktifkan secara global';
        return back()->with('success', "Modul {$module->module_name} berhasil {$stateText}.");
    }

    public function updateRwAccess(Request $request, $id)
    {
        $module = PlatformModuleConfig::findOrFail($id);
        $disabledRws = $request->input('disabled_rw_numbers', []);

        $module->disabled_rw_numbers = is_array($disabledRws) ? $disabledRws : [];
        $module->save();

        AuditLogger::log(
            'UPDATE_MODULE_RW_ACCESS',
            'Super Admin Platform',
            $module->id,
            "Super Admin (" . Auth::user()->name . ") memperbarui pengecualian RW untuk modul {$module->module_name}",
            null,
            ['disabled_rw_numbers' => $module->disabled_rw_numbers],
            null,
            'success',
            'CONFIG_CHANGE'
        );

        return back()->with('success', "Konfigurasi pembatasan RW untuk modul {$module->module_name} berhasil diperbarui.");
    }
}
