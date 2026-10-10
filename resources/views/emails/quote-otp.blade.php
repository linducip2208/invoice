<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <p>{{ __('Use this code to proceed with quotation :no:', ['no' => $quote->quote_no]) }}</p>
    <p style="font-size: 28px; font-weight: bold; letter-spacing: 6px;">{{ $otp }}</p>
    <p>{{ __('This code expires in 15 minutes and is bound to this quotation revision only.') }}</p>
    <p>{{ __('If you did not request this, ignore this email — opening the quotation link alone never approves anything.') }}</p>
</body>
</html>
