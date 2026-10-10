<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <p>{{ __('Quotation :no (revision :rev) was :outcome at :at (UTC).', [
        'no' => $quote->quote_no, 'rev' => $event->revision,
        'outcome' => $event->outcome, 'at' => $event->decided_at,
    ]) }}</p>
    <ul>
        <li>{{ __('Total: :cur :amt', ['cur' => $quote->currency, 'amt' => number_format((float) $quote->total, 2)]) }}</li>
        <li>{{ __('Approver: :name (:email)', ['name' => $event->approver_name, 'email' => $event->approver_email]) }}</li>
        <li>{{ __('Verification: :m', ['m' => $event->verification_method]) }}</li>
        <li>{{ __('Document hash: :h', ['h' => $event->document_sha256]) }}</li>
    </ul>
</body>
</html>
