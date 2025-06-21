@extends('layouts.master')
@section('page_title', 'Admit Student')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Please fill The form To Admit A New Student</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <form id="ajax-reg" method="post" enctype="multipart/form-data" class="wizard-form steps-validation" action="{{ route('students.store') }}" data-fouc>
        @csrf

        <!-- School selection field -->
        @if(Qs::userIsSuperAdmin() && isset($schools) && $schools->count() > 1)
            <div class="form-group">
                <label for="school_id">School: <span class="text-danger">*</span></label>
                <select name="school_id" id="school_id" class="form-control select" required>
                    <option value="">Select School</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <h6>Personal data</h6>
        <fieldset>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Full Name: <span class="text-danger">*</span></label>
                        <input value="{{ old('name') }}" required type="text" name="name" placeholder="Full Name" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Address: <span class="text-danger">*</span></label>
                        <input value="{{ old('address') }}" class="form-control" placeholder="Address" name="address" type="text" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Email address: </label>
                        <input type="email" value="{{ old('email') }}" name="email" class="form-control" placeholder="Email Address">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="gender">Gender: <span class="text-danger">*</span></label>
                        <select class="select form-control" id="gender" name="gender" required data-fouc data-placeholder="Choose..">
                            <option value=""></option>
                            <option {{ (old('gender') == 'Male') ? 'selected' : '' }} value="Male">Male</option>
                            <option {{ (old('gender') == 'Female') ? 'selected' : '' }} value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Phone:</label>
                        <input value="{{ old('phone') }}" type="text" name="phone" class="form-control" placeholder="">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Telephone:</label>
                        <input value="{{ old('phone2') }}" type="text" name="phone2" class="form-control" placeholder="">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date of Birth:</label>
                        <input name="dob" value="{{ old('dob') }}" type="text" class="form-control date-pick" placeholder="Select Date...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nal_id">Nationality: <span class="text-danger">*</span></label>
                        <select data-placeholder="Choose..." required name="nal_id" id="nal_id" class="select-search form-control">
                            <option value=""></option>
                            @if(isset($nationals) && $nationals && $nationals->count() > 0)
                                @foreach($nationals as $nal)
                                    <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="{{ $nal->id }}">{{ $nal->name ?? 'Unknown Nationality' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="state_id">State: <span class="text-danger">*</span></label>
                    <select onchange="getLGA(this.value)" required data-placeholder="Choose.." class="select-search form-control" name="state_id" id="state_id">
                        <option value=""></option>
                        @if(isset($states) && $states && $states->count() > 0)
                            @foreach($states as $st)
                                <option {{ (old('state_id') == $st->id ? 'selected' : '') }} value="{{ $st->id }}">{{ $st->name ?? 'Unknown State' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="lga_id">LGA: <span class="text-danger">*</span></label>
                    <select required data-placeholder="Select State First" class="select-search form-control" name="lga_id" id="lga_id">
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bg_id">Blood Group: </label>
                        <select class="select form-control" id="bg_id" name="bg_id" data-fouc data-placeholder="Choose..">
                            <option value=""></option>
                            @foreach(App\Models\BloodGroup::all() as $bg)
                                <option {{ (old('bg_id') == $bg->id ? 'selected' : '') }} value="{{ $bg->id }}">{{ $bg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="d-block">Upload Passport Photo:</label>
                        <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                        <span class="form-text text-muted">Accepted Images: jpeg, png. Max file size 2Mb</span>
                    </div>
                </div>
            </div>
        </fieldset>

        <h6>Student Data</h6>
        <fieldset>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="my_class_id">Class: <span class="text-danger">*</span></label>
                        <select onchange="getClassSections(this.value)" data-placeholder="Choose..." required name="my_class_id" id="my_class_id" class="select-search form-control">
                            <option value="">Select Class</option>
                            @if(isset($my_classes) && $my_classes && $my_classes->count() > 0)
                                @foreach($my_classes as $c)
                                    <option {{ (old('my_class_id') == $c->id ? 'selected' : '') }} value="{{ $c->id }}">{{ $c->name ?? 'Unknown Class' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="section_id">Section: <span class="text-danger">*</span></label>
                        <select data-placeholder="Select Class First" required name="section_id" id="section_id" class="select-search form-control">
                            <option value="">Select Section</option>
                        </select>
                        <div id="section-loading" class="text-info mt-1" style="display: none;">
                            <i class="icon-spinner2 spinner"></i> Loading sections...
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="my_parent_id">Parent: </label>
                        <select data-placeholder="Choose..." name="my_parent_id" id="my_parent_id" class="select-search form-control">
                            <option value=""></option>
                            @if(isset($parents) && $parents && $parents->count() > 0)
                                @foreach($parents as $p)
                                    <option {{ (old('my_parent_id') == Qs::hash($p->id)) ? 'selected' : '' }} value="{{ Qs::hash($p->id) }}">{{ $p->name ?? 'Unknown Parent' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year_admitted">Year Admitted: <span class="text-danger">*</span></label>
                        <select data-placeholder="Choose..." required name="year_admitted" id="year_admitted" class="select-search form-control">
                            <option value=""></option>
                            @for($y=date('Y', strtotime('- 10 years')); $y<=date('Y'); $y++)
                                <option {{ (old('year_admitted') == $y) ? 'selected' : '' }} value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label for="dorm_id">Dormitory: </label>
                    <select data-placeholder="Choose..." name="dorm_id" id="dorm_id" class="select-search form-control">
                        <option value=""></option>
                        @if(isset($dorms) && $dorms && $dorms->count() > 0)
                            @foreach($dorms as $d)
                                <option {{ (old('dorm_id') == $d->id) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name ?? 'Unknown Dorm' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dormitory Room No:</label>
                        <input type="text" name="dorm_room_no" placeholder="Dormitory Room No" class="form-control" value="{{ old('dorm_room_no') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sport House:</label>
                        <input type="text" name="house" placeholder="Sport House" class="form-control" value="{{ old('house') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Admission Number:</label>
                        <input type="text" name="adm_no" placeholder="Admission Number" class="form-control" value="{{ old('adm_no') }}">
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/state_lga_filter.js') }}"></script>
<script>
/**
 * Get sections for selected class
 */
function getClassSections(classId) {
    const sectionSelect = document.getElementById('section_id');
    const loadingDiv = document.getElementById('section-loading');

    // Reset section dropdown
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    if (!classId) {
        sectionSelect.setAttribute('data-placeholder', 'Select Class First');
        return;
    }

    // Show loading indicator
    if (loadingDiv) {
        loadingDiv.style.display = 'block';
    }

    // Make AJAX request
    fetch('{{ route("students.get-class-sections") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            class_id: classId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Hide loading indicator
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        if (data.success && data.sections) {
            // Clear existing options
            sectionSelect.innerHTML = '<option value="">Select Section</option>';

            // Add new options
            data.sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = section.name;
                sectionSelect.appendChild(option);
            });

            sectionSelect.setAttribute('data-placeholder', 'Choose Section...');

            // Trigger change event if using Select2 or similar
            if (typeof $(sectionSelect).trigger === 'function') {
                $(sectionSelect).trigger('change');
            }
        } else {
            sectionSelect.innerHTML = '<option value="">No sections found</option>';
        }
    })
    .catch(error => {
        console.error('Error fetching sections:', error);

        // Hide loading indicator
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        sectionSelect.innerHTML = '<option value="">Error loading sections</option>';

        // Show user-friendly error message
        if (typeof swal !== 'undefined') {
            swal('Error', 'Failed to load sections. Please try again.', 'error');
        } else {
            alert('Failed to load sections. Please try again.');
        }
    });
}

// Handle form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ajax-reg');
    if (form) {
        form.addEventListener('submit', function(e) {
            const classSelect = document.getElementById('my_class_id');
            const sectionSelect = document.getElementById('section_id');

            if (classSelect.value && !sectionSelect.value) {
                e.preventDefault();
                if (typeof swal !== 'undefined') {
                    swal('Warning', 'Please select a section for the chosen class.', 'warning');
                } else {
                    alert('Please select a section for the chosen class.');
                }
                sectionSelect.focus();
                return false;
            }
        });
    }

    // Auto-load sections if class is pre-selected (for edit forms)
    const classSelect = document.getElementById('my_class_id');
    if (classSelect && classSelect.value) {
        getClassSections(classSelect.value);
    }
});
</script>
@endsection
