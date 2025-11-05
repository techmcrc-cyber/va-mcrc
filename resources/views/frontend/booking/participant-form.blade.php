@php
    $isPrimary = isset($isPrimary) && $isPrimary;
@endphp

<div class="row">
    {{-- Common fields for all participants --}}
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_firstname" class="form-label required">First Name</label>
        <input type="text" name="participants[{{ $index }}][firstname]" id="participants_{{ $index }}_firstname" 
               class="form-control" value="{{ old("participants.$index.firstname") }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_lastname" class="form-label required">Last Name</label>
        <input type="text" name="participants[{{ $index }}][lastname]" id="participants_{{ $index }}_lastname" 
               class="form-control" value="{{ old("participants.$index.lastname") }}" required>
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label required">WhatsApp Number</label>
        <div class="input-group">
            <select name="participants[{{ $index }}][country_code]" id="participants_{{ $index }}_country_code" 
                    class="form-select" style="max-width: 180px;" required>
                <option value="+91" {{ old("participants.$index.country_code", '+91') == '+91' ? 'selected' : '' }}>+91 (India)</option>
                <option value="+1" {{ old("participants.$index.country_code") == '+1' ? 'selected' : '' }}>+1 (USA/Canada)</option>
                <option value="+44" {{ old("participants.$index.country_code") == '+44' ? 'selected' : '' }}>+44 (UK)</option>
                <option value="+971" {{ old("participants.$index.country_code") == '+971' ? 'selected' : '' }}>+971 (UAE)</option>
                <option value="+966" {{ old("participants.$index.country_code") == '+966' ? 'selected' : '' }}>+966 (Saudi Arabia)</option>
                <option value="+965" {{ old("participants.$index.country_code") == '+965' ? 'selected' : '' }}>+965 (Kuwait)</option>
                <option value="+974" {{ old("participants.$index.country_code") == '+974' ? 'selected' : '' }}>+974 (Qatar)</option>
                <option value="+973" {{ old("participants.$index.country_code") == '+973' ? 'selected' : '' }}>+973 (Bahrain)</option>
                <option value="+968" {{ old("participants.$index.country_code") == '+968' ? 'selected' : '' }}>+968 (Oman)</option>
                <option value="+61" {{ old("participants.$index.country_code") == '+61' ? 'selected' : '' }}>+61 (Australia)</option>
                <option value="+64" {{ old("participants.$index.country_code") == '+64' ? 'selected' : '' }}>+64 (New Zealand)</option>
                <option value="+65" {{ old("participants.$index.country_code") == '+65' ? 'selected' : '' }}>+65 (Singapore)</option>
                <option value="+60" {{ old("participants.$index.country_code") == '+60' ? 'selected' : '' }}>+60 (Malaysia)</option>
                <option value="+27" {{ old("participants.$index.country_code") == '+27' ? 'selected' : '' }}>+27 (South Africa)</option>
                <option value="+49" {{ old("participants.$index.country_code") == '+49' ? 'selected' : '' }}>+49 (Germany)</option>
                <option value="+33" {{ old("participants.$index.country_code") == '+33' ? 'selected' : '' }}>+33 (France)</option>
                <option value="+39" {{ old("participants.$index.country_code") == '+39' ? 'selected' : '' }}>+39 (Italy)</option>
                <option value="+34" {{ old("participants.$index.country_code") == '+34' ? 'selected' : '' }}>+34 (Spain)</option>
            </select>
            <input type="text" name="participants[{{ $index }}][whatsapp_number]" id="participants_{{ $index }}_whatsapp_number" 
                   class="form-control whatsapp-input" value="{{ old("participants.$index.whatsapp_number") }}" 
                   maxlength="15" pattern="[0-9]{7,15}" required>
        </div>
        <small class="text-muted">Enter phone number without country code</small>
    </div>
    <div class="col-md-3 mb-3">
        <label for="participants_{{ $index }}_age" class="form-label required">Age</label>
        <input type="number" name="participants[{{ $index }}][age]" id="participants_{{ $index }}_age" 
               class="form-control" value="{{ old("participants.$index.age") }}" 
               min="1" max="120" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="participants_{{ $index }}_gender" class="form-label required">Gender</label>
        <select name="participants[{{ $index }}][gender]" id="participants_{{ $index }}_gender" 
                class="form-select" required>
            <option value="">Select</option>
            <option value="male" {{ old("participants.$index.gender") == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old("participants.$index.gender") == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old("participants.$index.gender") == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_married" class="form-label">Marital Status</label>
        <select name="participants[{{ $index }}][married]" id="participants_{{ $index }}_married" 
                class="form-select">
            <option value="">Select</option>
            <option value="yes" {{ old("participants.$index.married") == 'yes' ? 'selected' : '' }}>Married</option>
            <option value="no" {{ old("participants.$index.married") == 'no' ? 'selected' : '' }}>Unmarried</option>
        </select>
    </div>
    <div class="col-md-6 mb-3 congregation-field" style="display: none;">
        <label for="participants_{{ $index }}_congregation" class="form-label">Congregation</label>
        <input type="text" name="participants[{{ $index }}][congregation]" id="participants_{{ $index }}_congregation" 
               class="form-control" value="{{ old("participants.$index.congregation") }}">
        <small class="text-muted">For Priests/Sisters only</small>
    </div>
    <div class="col-md-12 mb-3">
        <label for="participants_{{ $index }}_email" class="form-label required">Email Address</label>
        <input type="email" name="participants[{{ $index }}][email]" id="participants_{{ $index }}_email" 
               class="form-control" value="{{ old("participants.$index.email") }}" required>
    </div>

    {{-- Additional fields only for primary participant --}}
    @if($isPrimary)
    <div class="col-12"><hr class="my-3"></div>
    <div class="col-12 mb-3">
        <h6 class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
    </div>
    <div class="col-md-12 mb-3">
        <label for="participants_{{ $index }}_address" class="form-label required">Address</label>
        <textarea name="participants[{{ $index }}][address]" id="participants_{{ $index }}_address" 
                  class="form-control" rows="2" required>{{ old("participants.$index.address") }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_city" class="form-label required">City</label>
        <input type="text" name="participants[{{ $index }}][city]" id="participants_{{ $index }}_city" 
               class="form-control" value="{{ old("participants.$index.city") }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_state" class="form-label required">State</label>
        <input type="text" name="participants[{{ $index }}][state]" id="participants_{{ $index }}_state" 
               class="form-control" value="{{ old("participants.$index.state") }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_diocese" class="form-label">Diocese</label>
        <input type="text" name="participants[{{ $index }}][diocese]" id="participants_{{ $index }}_diocese" 
               class="form-control" value="{{ old("participants.$index.diocese") }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_parish" class="form-label">Parish</label>
        <input type="text" name="participants[{{ $index }}][parish]" id="participants_{{ $index }}_parish" 
               class="form-control" value="{{ old("participants.$index.parish") }}">
    </div>
    
    <div class="col-12"><hr class="my-3"></div>
    <div class="col-12 mb-3">
        <h6 class="text-muted"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h6>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_emergency_contact_name" class="form-label required">Emergency Contact Name</label>
        <input type="text" name="participants[{{ $index }}][emergency_contact_name]" id="participants_{{ $index }}_emergency_contact_name" 
               class="form-control" value="{{ old("participants.$index.emergency_contact_name") }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="participants_{{ $index }}_emergency_contact_phone" class="form-label required">Emergency Contact Phone</label>
        <input type="text" name="participants[{{ $index }}][emergency_contact_phone]" id="participants_{{ $index }}_emergency_contact_phone" 
               class="form-control" value="{{ old("participants.$index.emergency_contact_phone") }}" required>
    </div>
    <div class="col-md-12 mb-3">
        <label for="participants_{{ $index }}_special_remarks" class="form-label">Special Remarks</label>
        <textarea name="participants[{{ $index }}][special_remarks]" id="participants_{{ $index }}_special_remarks" 
                  class="form-control" rows="2">{{ old("participants.$index.special_remarks") }}</textarea>
        <small class="text-muted">Any dietary restrictions, medical conditions, or special requirements</small>
    </div>
    @else
    <div class="col-12 mt-2">
        <p class="text-muted small mb-0">
            <i class="fas fa-info-circle me-1"></i>
            Address and emergency contact will be copied from primary participant
        </p>
    </div>
    @endif
</div>
