<?php

namespace App\Services\Approval;

use App\Models\ApprovalRequest;
use App\Models\ApprovalRule;
use Illuminate\Database\Eloquent\Model;

class ApprovalEngine
{
    /**
     * Evaluate rules for a subject and create an approval request if any matches.
     * Returns the request, or null if no rule applies.
     */
    public function evaluate(Model $subject, string $type, float $amount = 0): ?ApprovalRequest
    {
        $rules = ApprovalRule::where('applies_to', $type)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $amount)) {
                return ApprovalRequest::create([
                    'workspace_id' => $subject->workspace_id ?? workspace()?->id,
                    'rule_id'      => $rule->id,
                    'subject_type' => get_class($subject),
                    'subject_id'   => $subject->getKey(),
                    'requested_by' => auth()->id(),
                    'status'       => 'pending',
                    'approver_responses' => [],
                ]);
            }
        }
        return null;
    }

    private function ruleMatches(ApprovalRule $rule, float $amount): bool
    {
        return match ($rule->condition_type) {
            'amount_gte' => $amount >= (float) $rule->condition_value,
            'always'     => true,
            default      => false,
        };
    }

    public function recordResponse(ApprovalRequest $request, int $userId, string $decision, ?string $comment = null): ApprovalRequest
    {
        if (! in_array($userId, $request->rule?->approver_user_ids ?? [])) {
            throw new \RuntimeException('Anda tidak berwenang approve request ini.');
        }

        $responses = $request->approver_responses ?? [];
        $responses[] = [
            'user_id'      => $userId,
            'decision'     => $decision,
            'comment'      => $comment,
            'responded_at' => now()->toIso8601String(),
        ];

        $request->approver_responses = $responses;

        $rule = $request->rule;
        $totalApprovers = count($rule?->approver_user_ids ?? []);
        $approvedCount = count(array_filter($responses, fn ($r) => $r['decision'] === 'approved'));
        $rejectedCount = count(array_filter($responses, fn ($r) => $r['decision'] === 'rejected'));

        if ($rejectedCount > 0) {
            $request->status = 'rejected';
            $request->final_decision_at = now();
        } elseif ($rule?->require_all && $approvedCount >= $totalApprovers) {
            $request->status = 'approved';
            $request->final_decision_at = now();
        } elseif (! $rule?->require_all && $approvedCount > 0) {
            $request->status = 'approved';
            $request->final_decision_at = now();
        }

        $request->save();
        return $request;
    }
}
