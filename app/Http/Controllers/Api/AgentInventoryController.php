<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryScan;
use App\Models\InventorySoftware;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AgentInventoryController extends Controller
{
    public function sync(Request $request)
    {
        $data = $request->validate([
            'partnerKey' => ['nullable', 'string', 'max:255'],
            'partnerOib' => ['nullable', 'string', 'max:50'],
            'deviceId' => ['required', 'string', 'max:255'],
            'scanType' => ['required', 'string', Rule::in(['quick', 'full'])],
            'inventory' => ['required', 'array'],
        ]);

        if (empty($data['partnerKey']) && empty($data['partnerOib'])) {
            return response()->json([
                'ok' => false,
                'message' => 'partnerKey ili partnerOib je obavezan.',
            ], 422);
        }

        $partner = $this->resolvePartner($data);

        if (! $partner) {
            return response()->json([
                'ok' => false,
                'message' => 'Partner nije pronađen.',
            ], 404);
        }

        if (! $partner->inventory_enabled) {
            return response()->json([
                'ok' => false,
                'message' => 'Inventory je isključen za ovog partnera.',
            ], 403);
        }

        if (! in_array($partner->inventory_mode, ['agent', 'hybrid'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Agent sync nije dopušten za ovog partnera.',
            ], 403);
        }

        $inventory = $data['inventory'];

        $item = DB::transaction(function () use ($partner, $data, $inventory) {
            $item = InventoryItem::firstOrNew([
                'partner_id' => $partner->id,
                'agent_device_id' => $data['deviceId'],
            ]);

            $item->source = 'agent';

            $item->hostname = data_get($inventory, 'hardware.hostname');
            $item->serial_number = data_get($inventory, 'hardware.serialNumber');
            $item->manufacturer = data_get($inventory, 'hardware.manufacturer');
            $item->model = data_get($inventory, 'hardware.model');

            $item->cpu = data_get($inventory, 'hardware.cpu');
            $item->cpu_cores = data_get($inventory, 'hardware.cpuCores');
            $item->cpu_threads = data_get($inventory, 'hardware.cpuThreads');
            $item->ram_gb = data_get($inventory, 'hardware.ramGb');
            $item->gpu = data_get($inventory, 'hardware.gpu');

            $item->system_drive = data_get($inventory, 'disk.systemDrive');
            $item->disk_total_gb = data_get($inventory, 'disk.totalGb');
            $item->disk_free_gb = data_get($inventory, 'disk.freeGb');

            $item->os_caption = data_get($inventory, 'operatingSystem.caption');
            $item->os_version = data_get($inventory, 'operatingSystem.version');
            $item->os_build_number = data_get($inventory, 'operatingSystem.buildNumber');
            $item->os_architecture = data_get($inventory, 'operatingSystem.architecture');
            $item->os_install_date = data_get($inventory, 'operatingSystem.installDate');
            $item->os_last_boot_time = data_get($inventory, 'operatingSystem.lastBootTime');

            $item->tpm_present = data_get($inventory, 'security.tpmPresent');
            $item->bitlocker_enabled = data_get($inventory, 'security.bitlockerEnabled');
            $item->windows_activated = data_get($inventory, 'security.windowsActivated');

            $item->vpn_detected = data_get($inventory, 'network.vpnDetected');
            $item->primary_adapter_name = data_get($inventory, 'network.primaryAdapterName');
            $item->primary_mac_address = data_get($inventory, 'network.primaryMacAddress');
            $item->primary_ip_address = data_get($inventory, 'network.primaryIpAddress');

            $item->is_domain_joined = data_get($inventory, 'domain.isDomainJoined');
            $item->domain_name = data_get($inventory, 'domain.domainName');
            $item->is_azure_ad_joined = data_get($inventory, 'domain.isAzureAdJoined');

            $item->windows_update_service_running = data_get($inventory, 'windowsUpdate.serviceRunning');
            $item->windows_update_service_status = data_get($inventory, 'windowsUpdate.serviceStatus');

            $item->current_user = data_get($inventory, 'agent.currentUser');
            $item->agent_version = data_get($inventory, 'agent.agentVersion');

            $item->last_seen_at = now();
            $item->raw_payload_json = json_encode($inventory, JSON_UNESCAPED_UNICODE);

            if (! $item->exists) {
                $item->status = 'active';
            }

            $item->save();

            $scan = InventoryScan::create([
                'inventory_item_id' => $item->id,
                'agent_device_id' => $data['deviceId'],
                'scan_type' => $data['scanType'],
                'agent_version' => data_get($inventory, 'agent.agentVersion'),
                'scan_started_at' => data_get($inventory, 'agent.scanStartedAt'),
                'scan_completed_at' => data_get($inventory, 'agent.scanCompletedAt'),
                'scan_duration_ms' => data_get($inventory, 'agent.scanDurationMs'),
                'payload_json' => json_encode($inventory, JSON_UNESCAPED_UNICODE),
            ]);

            if ($data['scanType'] === 'full') {
                InventorySoftware::where('inventory_item_id', $item->id)->delete();

                foreach ((array) data_get($inventory, 'installedSoftware', []) as $software) {
                    $name = trim((string) data_get($software, 'name', ''));

                    if ($name === '') {
                        continue;
                    }

                    InventorySoftware::create([
                        'inventory_item_id' => $item->id,
                        'inventory_scan_id' => $scan->id,
                        'name' => $name,
                        'version' => data_get($software, 'version'),
                        'publisher' => data_get($software, 'publisher'),
                        'install_date' => data_get($software, 'installDate'),
                    ]);
                }
            }

            return $item->fresh(['partner', 'software', 'scans']);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Inventory sync uspješno zaprimljen.',
            'partner' => [
                'id' => $item->partner->id,
                'name' => $item->partner->name,
            ],
            'item' => [
                'id' => $item->id,
                'hostname' => $item->hostname,
                'model' => $item->model,
                'serial_number' => $item->serial_number,
                'source' => $item->source,
                'agent_device_id' => $item->agent_device_id,
                'software_count' => $item->software->count(),
                'scan_count' => $item->scans->count(),
            ],
        ]);
    }

    private function resolvePartner(array $data): ?Partner
    {
        if (! empty($data['partnerKey'])) {
            $partner = Partner::query()
                ->where('inventory_partner_key', $data['partnerKey'])
                ->first();

            if ($partner) {
                return $partner;
            }
        }

        if (! empty($data['partnerOib'])) {
            return Partner::query()
                ->where('oib', preg_replace('/\D+/', '', $data['partnerOib']))
                ->first();
        }

        return null;
    }
}