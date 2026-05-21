<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRulesManagerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->integer('per_page', 20)));

        return response()->json([
            'success' => true,
            'data' => [
                'symptom_rules' => DB::table('medical_symptom_rules')->latest('id')->paginate($perPage),
                'contraindication_rules' => DB::table('medical_contraindication_rules')->latest('id')->paginate($perPage),
                'interaction_blacklists' => DB::table('drug_interaction_blacklists')->latest('id')->paginate($perPage),
                'latest_release' => DB::table('medical_rule_releases')->latest('id')->first(),
            ],
        ]);
    }

    public function storeSymptomRule(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'keyword' => ['required', 'string', 'max:120'],
            'drug_name' => ['required', 'string', 'max:120'],
            'drug_type' => ['required', 'in:OTC,REFER'],
            'indication' => ['required', 'string', 'max:255'],
            'severity_score' => ['required', 'integer', 'between:1,5'],
            'warnings' => ['nullable', 'array'],
        ]);

        $id = DB::table('medical_symptom_rules')->insertGetId([
            ...$payload,
            'warnings' => json_encode($payload['warnings'] ?? []),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->audit('create', 'medical_symptom_rules', $id, null, $payload, $request);

        return response()->json(['success' => true, 'id' => $id], 201);
    }

    public function updateSymptomRule(Request $request, int $id): JsonResponse
    {
        $payload = $request->validate([
            'keyword' => ['sometimes', 'string', 'max:120'],
            'drug_name' => ['sometimes', 'string', 'max:120'],
            'drug_type' => ['sometimes', 'in:OTC,REFER'],
            'indication' => ['sometimes', 'string', 'max:255'],
            'severity_score' => ['sometimes', 'integer', 'between:1,5'],
            'warnings' => ['nullable', 'array'],
        ]);

        $before = (array) DB::table('medical_symptom_rules')->where('id', $id)->first();
        if (!$before) abort(404, 'Rule not found');

        $update = $payload;
        if (array_key_exists('warnings', $update)) {
            $update['warnings'] = json_encode($update['warnings'] ?? []);
        }
        $update['updated_at'] = now();

        DB::table('medical_symptom_rules')->where('id', $id)->update($update);
        $this->audit('update', 'medical_symptom_rules', $id, $before, $update, $request);

        return response()->json(['success' => true]);
    }

    public function destroySymptomRule(Request $request, int $id): JsonResponse
    {
        $before = (array) DB::table('medical_symptom_rules')->where('id', $id)->first();
        if (!$before) abort(404, 'Rule not found');
        DB::table('medical_symptom_rules')->where('id', $id)->delete();
        $this->audit('delete', 'medical_symptom_rules', $id, $before, null, $request);
        return response()->json(['success' => true]);
    }

    public function publishRelease(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'version' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $snapshot = [
            'symptom_rules' => DB::table('medical_symptom_rules')->count(),
            'contraindication_rules' => DB::table('medical_contraindication_rules')->count(),
            'interaction_blacklists' => DB::table('drug_interaction_blacklists')->count(),
        ];

        $id = DB::table('medical_rule_releases')->insertGetId([
            'version' => $payload['version'],
            'notes' => $payload['notes'] ?? null,
            'snapshot' => json_encode($snapshot),
            'created_by' => $request->user()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'release_id' => $id, 'snapshot' => $snapshot], 201);
    }

    private function audit(string $action, string $tableName, int $recordId, mixed $before, mixed $after, Request $request): void
    {
        DB::table('medical_rule_audits')->insert([
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'before_data' => $before ? json_encode($before) : null,
            'after_data' => $after ? json_encode($after) : null,
            'performed_by' => $request->user()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
