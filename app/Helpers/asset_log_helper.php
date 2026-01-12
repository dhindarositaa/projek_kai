<?php

use App\Models\AssetLogModel;

function log_asset_changes($assetId, $old, $new, $action = 'update')
{
    $logModel = new AssetLogModel();
    $request = service('request');
    $userId = session()->get('user_id');

    foreach ($new as $field => $value) {
        if (!array_key_exists($field, $old)) continue;

        $oldVal = (string) ($old[$field] ?? '');
        $newVal = (string) ($value ?? '');

        if ($oldVal !== $newVal) {
            $logModel->insert([
                'asset_id' => $assetId,
                'user_id' => $userId,
                'field' => $field,
                'old_value' => $oldVal,
                'new_value' => $newVal,
                'action' => $action,
                'ip_address' => $request->getIPAddress(),
            ]);
        }
    }
}
